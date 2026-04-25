import io from 'socket.io-client';

const SOCKET_URL = import.meta.env.VITE_SOCKET_URL || 'http://localhost:3000';

class RealtimeClient {
    constructor() {
        this.socket = null;
        this.listeners = new Map();
        this.isConnected = false;
    }

    connect(token = null) {
        if (this.socket) {
            return this.socket;
        }

        const options = {
            reconnection: true,
            reconnectionDelay: 1000,
            reconnectionDelayMax: 5000,
            reconnectionAttempts: 5,
            transports: ['websocket', 'polling'],
        };

        if (token) {
            options.auth = { token };
        } else {
            // Get token from localStorage
            const authToken = localStorage.getItem('auth_token');
            if (authToken) {
                options.auth = { token: authToken };
            }
        }

        this.socket = io(SOCKET_URL, options);

        // Connection events
        this.socket.on('connect', () => {
            console.log('✅ Realtime connected');
            this.isConnected = true;
            this.emit('connected');
        });

        this.socket.on('disconnect', () => {
            console.log('❌ Realtime disconnected');
            this.isConnected = false;
            this.emit('disconnected');
        });

        this.socket.on('error', (error) => {
            console.error('⚠️ Realtime error:', error);
            this.emit('error', error);
        });

        this.socket.on('connect_error', (error) => {
            console.error('⚠️ Connection error:', error);
        });

        // Message events
        this.socket.on('direct_message:received', (data) => {
            this.emit('direct_message:received', data);
        });

        this.socket.on('group_message:received', (data) => {
            this.emit('group_message:received', data);
        });

        // Typing indicators
        this.socket.on('typing:start', (data) => {
            this.emit('typing:start', data);
        });

        this.socket.on('typing:stop', (data) => {
            this.emit('typing:stop', data);
        });

        // User status
        this.socket.on('user:status_changed', (data) => {
            this.emit('user:status_changed', data);
        });

        // Video call events
        this.socket.on('call:incoming', (data) => {
            this.emit('call:incoming', data);
        });

        this.socket.on('call:accepted', (data) => {
            this.emit('call:accepted', data);
        });

        this.socket.on('call:rejected', (data) => {
            this.emit('call:rejected', data);
        });

        this.socket.on('call:ended', (data) => {
            this.emit('call:ended', data);
        });

        // Task notifications
        this.socket.on('task:updated', (data) => {
            this.emit('task:updated', data);
        });

        this.socket.on('task:commented', (data) => {
            this.emit('task:commented', data);
        });

        this.socket.on('task:assigned', (data) => {
            this.emit('task:assigned', data);
        });

        // Notifications
        this.socket.on('notification:new', (data) => {
            this.emit('notification:new', data);
        });

        return this.socket;
    }

    disconnect() {
        if (this.socket) {
            this.socket.disconnect();
            this.socket = null;
            this.isConnected = false;
        }
    }

    // Emit listener
    on(event, callback) {
        if (!this.listeners.has(event)) {
            this.listeners.set(event, []);
        }
        this.listeners.get(event).push(callback);
    }

    // Remove listener
    off(event, callback) {
        if (!this.listeners.has(event)) return;
        const callbacks = this.listeners.get(event);
        const index = callbacks.indexOf(callback);
        if (index > -1) {
            callbacks.splice(index, 1);
        }
    }

    // Emit event to listeners
    emit(event, data) {
        if (this.listeners.has(event)) {
            this.listeners.get(event).forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                    console.error(`Error in listener for ${event}:`, error);
                }
            });
        }
    }

    // Send direct message
    sendDirectMessage(conversationId, message) {
        this.socket?.emit('direct_message:send', { conversationId, message });
    }

    // Send group message
    sendGroupMessage(groupId, message) {
        this.socket?.emit('group_message:send', { groupId, message });
    }

    // Typing indicators
    startTyping(conversationId) {
        this.socket?.emit('typing:start', { conversationId });
    }

    stopTyping(conversationId) {
        this.socket?.emit('typing:stop', { conversationId });
    }

    // Update user status
    updateStatus(status) {
        this.socket?.emit('user:status_update', { status });
    }

    // Call methods
    initiateCall(userId, type = 'video') {
        this.socket?.emit('call:initiate', { user_id: userId, type });
    }

    acceptCall(callId) {
        this.socket?.emit('call:accept', { call_id: callId });
    }

    rejectCall(callId) {
        this.socket?.emit('call:reject', { call_id: callId });
    }

    endCall(callId) {
        this.socket?.emit('call:end', { call_id: callId });
    }

    // ICE candidate for WebRTC
    addIceCandidate(callId, candidate) {
        this.socket?.emit('call:ice-candidate', { call_id: callId, candidate });
    }

    // Send SDP offer
    sendOffer(callId, offer) {
        this.socket?.emit('call:offer', { call_id: callId, offer });
    }

    // Send SDP answer
    sendAnswer(callId, answer) {
        this.socket?.emit('call:answer', { call_id: callId, answer });
    }

    // Join room
    joinRoom(roomId) {
        this.socket?.emit('room:join', { room_id: roomId });
    }

    // Leave room
    leaveRoom(roomId) {
        this.socket?.emit('room:leave', { room_id: roomId });
    }
}

// Singleton instance
let realtimeInstance = null;

export function useRealtime() {
    if (!realtimeInstance) {
        realtimeInstance = new RealtimeClient();
    }
    return realtimeInstance;
}

export default RealtimeClient;
