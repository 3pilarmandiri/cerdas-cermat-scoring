// server.js
const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express();
const server = http.createServer(app);
const PORT = 3003;

// --- Allowed Origins ---
const allowedOrigins = [
  'http://localhost:3000',       // typical Next.js dev
  'http://localhost:3001',       // if you're using custom port
  'http://127.0.0.1:3000',
  'http://127.0.0.1:3001',
  'http://host.docker.internal:3000',
  'http://host.docker.internal:3001',
];

// --- Middleware CORS untuk Express ---
app.use(cors({
  origin: function (origin, callback) {
    // Izinkan permintaan tanpa origin (seperti dari Postman)
    if (!origin) return callback(null, true);
    if (allowedOrigins.includes(origin)) {
      return callback(null, true);
    } else {
      console.log(`🚫 Blocked by CORS: ${origin}`);
      return callback(new Error('CORS not allowed'));
    }
  },
  methods: ['GET', 'POST'],
  credentials: true,
}));
app.use(bodyParser.json());

// --- Socket.IO CORS ---
const io = socketIo(server, {
  cors: {
    origin: allowedOrigins,
    methods: ['GET', 'POST'],
    credentials: true,
  },
});

// --- WebSocket Logic ---
io.on('connection', (socket) => {
  console.log(`✅ User connected: ${socket.id}`);
  socket.emit('updateData', latestData); // kirim data terakhir segera

  socket.on('disconnect', () => {
    console.log(`❌ User disconnected: ${socket.id}`);
  });
});

// --- Latest data memory ---
let latestData = { A: '0', B: '0', C: '0', D: '0' };

// --- HTTP Endpoints ---
app.post('/broadcast-data', (req, res) => {
  const dataToBroadcast = req.body;
  if (!dataToBroadcast || typeof dataToBroadcast !== 'object') {
    return res.status(400).json({ error: 'Invalid JSON data format.' });
  }

  latestData = dataToBroadcast;
  io.emit('updateData', latestData);
  console.log('📢 Broadcasted:', latestData);

  res.json({ message: 'Broadcast success', data: latestData });
});

app.get('/latest-data', (req, res) => {
  res.json(latestData);
});

// --- Run Server ---
server.listen(PORT, () => {
  console.log(`🚀 WebSocket Server running on http://localhost:${PORT}`);
});
