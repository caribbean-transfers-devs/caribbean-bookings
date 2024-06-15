const express = require('express');
const http = require('http');
const socketIo = require('socket.io');

const app = express();
const server = http.createServer(app);
const io = socketIo(server, {
    cors: {
        origin: "http://127.0.0.1:8001",
        methods: ["GET", "POST"]
    }
});

io.on('connection', (socket) => {
  console.log('Nuevo cliente conectado');

  socket.on('updateData', (data) => {
    io.emit('dataUpdated', data);
  });

  socket.on('disconnect', () => {
    console.log('Cliente desconectado');
  });
});

server.listen(3000, () => {
  console.log('Servidor corriendo en puerto 3000');
});

// const server = require('http').createServer();
// const io = require('socket.io')(server, {
//     cors: {
//         origin: '*',
//     }
// });

// io.on('connection', socket => {
//     console.log('A user connected');

//     socket.on('disconnect', () => {
//         console.log('User disconnected');
//     });
// });

// server.listen(3000, () => {
//     console.log('Socket.IO server running on port 3000');
// });