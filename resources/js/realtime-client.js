export default class RealtimeClient {
    constructor(url = 'ws://127.0.0.1:8081') {
        this.url = url;
        this.socket = null;
        this.timer = null;
        this.subscriptions = new Set();
        this.listeners = new Map(); 

        this.connect();
    }

    connect() {
        if (this.socket) {
            return;
        }

        this.socket = new WebSocket(this.url);

        this.socket.onopen = () => {
            console.log('Realtime connected');
            if (this.subscriptions.size > 0) {
                this.resubscribe();
            }
        };

        this.socket.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                if (data.event) {
                    this.dispatch(data.event, data.payload);
                }
            } catch (e) {
                console.error('Realtime message error', e);
            }
        };

        this.socket.onclose = () => {
            console.log('Realtime disconnected, reconnecting...');
            this.socket = null;
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.connect(), 3000);
        };

        this.socket.onerror = (err) => {
            console.error('Realtime socket error', err);
            this.socket.close();
        };
    }

    subscribe(channels) {
        const list = Array.isArray(channels) ? channels : [channels];
        list.forEach(c => this.subscriptions.add(c));
        this.resubscribe();
    }

    resubscribe() {
        if (this.socket && this.socket.readyState === WebSocket.OPEN && this.subscriptions.size > 0) {
            this.socket.send(JSON.stringify({
                action: 'subscribe',
                channels: Array.from(this.subscriptions)
            }));
        }
    }

    on(event, callback) {
        if (!this.listeners.has(event)) {
            this.listeners.set(event, []);
        }
        this.listeners.get(event).push(callback);
    }

    off(event, callback) {
        if (!this.listeners.has(event)) return;
        const filtered = this.listeners.get(event).filter(cb => cb !== callback);
        this.listeners.set(event, filtered);
    }

    dispatch(event, payload) {
        if (this.listeners.has(event)) {
            this.listeners.get(event).forEach(cb => cb(payload));
        }
    }
}
