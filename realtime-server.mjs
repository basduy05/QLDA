import http from 'node:http';
import { URL } from 'node:url';
import { WebSocketServer } from 'ws';

const port = Number(process.env.REALTIME_PORT || process.env.PORT || 8081);
const host = process.env.REALTIME_HOST || '0.0.0.0';
const secret = process.env.REALTIME_SECRET || '';

const subscriptions = new Map();

const addSubscriptions = (socket, channels) => {
    if (!subscriptions.has(socket)) {
        subscriptions.set(socket, new Set());
    }

    const socketChannels = subscriptions.get(socket);
    channels
        .map((value) => String(value || '').trim())
        .filter(Boolean)
        .forEach((channel) => socketChannels.add(channel));
};

const parseChannelsFromUrl = (requestUrl) => {
    const parsed = new URL(requestUrl, 'http://localhost');
    const repeated = parsed.searchParams.getAll('channel');

    if (repeated.length > 0) {
        return repeated;
    }

    const inline = parsed.searchParams.get('channels');
    if (!inline) {
        return [];
    }

    return inline.split(',').map((item) => item.trim());
};

const server = http.createServer((req, res) => {
    const requestUrl = new URL(req.url || '/', 'http://localhost');

    if (req.method === 'GET' && requestUrl.pathname === '/health') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ ok: true }));
        return;
    }

    if (req.method === 'POST' && requestUrl.pathname === '/broadcast') {
        let body = '';

        req.on('data', (chunk) => {
            body += chunk;
            if (body.length > 1_000_000) {
                req.destroy();
            }
        });

        req.on('end', () => {
            try {
                const payload = JSON.parse(body || '{}');

                if (secret && payload.secret !== secret) {
                    res.writeHead(403, { 'Content-Type': 'application/json' });
                    res.end(JSON.stringify({ ok: false, message: 'Forbidden' }));
                    return;
                }

                const channels = Array.isArray(payload.channels) ? payload.channels : [];
                const eventName = String(payload.event || '').trim();

                if (channels.length === 0 || eventName === '') {
                    res.writeHead(422, { 'Content-Type': 'application/json' });
                    res.end(JSON.stringify({ ok: false, message: 'Invalid payload' }));
                    return;
                }

                const packet = JSON.stringify({
                    event: eventName,
                    payload: payload.payload || {},
                    channels,
                    ts: Date.now(),
                });

                for (const [socket, socketChannels] of subscriptions.entries()) {
                    if (socket.readyState !== socket.OPEN) {
                        continue;
                    }

                    const canReceive = channels.some((channel) => socketChannels.has(String(channel)));
                    if (canReceive) {
                        socket.send(packet);
                    }
                }

                res.writeHead(200, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ ok: true }));
            } catch {
                res.writeHead(400, { 'Content-Type': 'application/json' });
                res.end(JSON.stringify({ ok: false, message: 'Invalid JSON' }));
            }
        });

        return;
    }

    res.writeHead(404, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ ok: false, message: 'Not Found' }));
});

const wsServer = new WebSocketServer({ server });

wsServer.on('connection', (socket, request) => {
    addSubscriptions(socket, parseChannelsFromUrl(request.url || '/'));

    socket.on('message', (message) => {
        try {
            const data = JSON.parse(String(message));
            if (data?.action === 'subscribe' && Array.isArray(data.channels)) {
                addSubscriptions(socket, data.channels);
            }
        } catch {
        }
    });

    socket.on('close', () => {
        subscriptions.delete(socket);
    });
});

server.listen(port, host, () => {
    console.log(`Realtime WS listening on ${host}:${port}`);
});
