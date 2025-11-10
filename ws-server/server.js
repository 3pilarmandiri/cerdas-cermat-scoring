// server.js
const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express();
const server = http.createServer(app);
const PORT = 3003;

// --- Middleware CORS: Allow all origins ---
app.use(cors({
  origin: '*',             // ⬅️ Izinkan semua host
  methods: ['GET', 'POST'],
  credentials: false,
}));
app.use(bodyParser.json());

// --- Socket.IO CORS ---
const io = socketIo(server, {
  cors: {
    origin: '*',           // ⬅️ Izinkan semua host
    methods: ['GET', 'POST'],
    credentials: false,
  },
});

// --- Latest data memory ---
let latestData = { A: '0', B: '0', C: '0', D: '0' };

// --- WebSocket Logic ---
io.on('connection', (socket) => {
  console.log(`✅ User connected: ${socket.id}`);
  socket.emit('updateData', latestData); // kirim data terakhir segera

  socket.on('disconnect', () => {
    console.log(`❌ User disconnected: ${socket.id}`);
  });
});

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
