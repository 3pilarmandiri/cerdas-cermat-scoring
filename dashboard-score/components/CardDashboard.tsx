"use client";

import React, { useState, useEffect } from "react";
import io, { Socket } from "socket.io-client";

const WS_URL = process.env.NEXT_PUBLIC_WS_URL || "http://localhost:3003";

interface CardProps {
  title: string;
  value: string;
  bgColor: string;
}

interface DashboardData {
  A: string;
  B: string;
  C: string;
  D: string;
}

// --- Komponen Card ---
const Card: React.FC<CardProps> = ({ title, value, bgColor }) => {
  const [animate, setAnimate] = useState(false);

  useEffect(() => {
    // Jalankan animasi setiap kali value berubah
    setAnimate(true);
    const timeout = setTimeout(() => setAnimate(false), 500);
    return () => clearTimeout(timeout);
  }, [value]);

  return (
    <div
      className={`rounded-xl shadow-lg transition-transform duration-300 h-full flex flex-col ${bgColor}`}
    >
      <div className="bg-white/30 rounded-t-xl py-10 text-center flex items-center justify-center">
  <h2 className="text-4xl font-bold text-white tracking-wide">{title}</h2>
</div>


      <div className="flex flex-col justify-center items-center bg-white/90 rounded-b-xl flex-grow p-4">
        <div
          className={`border-4 border-gray-700 rounded-full w-56 h-56 flex items-center justify-center 
            transition-transform duration-500 ease-in-out ${
              animate ? "scale-110" : "scale-100"
            }`}
        >
          <span
            className={`text-8xl font-bold text-gray-800 transition-transform duration-500 ${
              animate ? "scale-110" : "scale-100"
            }`}
          >
            {value}
          </span>
        </div>
      </div>
    </div>
  );
};

// --- Komponen Dashboard ---
const CardDashboard: React.FC = () => {
  const [data, setData] = useState<DashboardData>({
    A: "0",
    B: "0",
    C: "0",
    D: "0",
  });
  const [isConnected, setIsConnected] = useState(false);
  const [statusMessage, setStatusMessage] = useState("Menghubungkan...");

  const [socket, setSocket] = useState<Socket | null>(null);

  const fetchLatestData = async () => {
    try {
      const response = await fetch(`${WS_URL}/latest-data`);
      if (!response.ok) throw new Error("Gagal mengambil data terakhir");
      const latestData = (await response.json()) as DashboardData;
      setData(latestData);
    } catch (error) {
      console.error("Error fetching latest data:", error);
      setStatusMessage("Gagal memuat data. Periksa server Node.js (3003).");
    }
  };

  useEffect(() => {
    fetchLatestData();

    const s = io(WS_URL, {
      reconnection: true,
      reconnectionAttempts: Infinity,
      reconnectionDelay: 1000,
    });
    setSocket(s);

    s.on("connect", () => {
      setIsConnected(true);
      setStatusMessage("Tersambung");
    });

    s.on("disconnect", () => {
      setIsConnected(false);
      setStatusMessage("Terputus. Mencoba menghubungkan kembali...");
    });

    s.on("updateData", (newData: DashboardData) => {
      if (newData && typeof newData === "object") setData(newData);
    });

    return () => {
      s.disconnect();
    };
  }, []);

  const cardConfig = [
    { title: "KELOMPOK A", key: "A", bgColor: "bg-red-500" },
    { title: "KELOMPOK B", key: "B", bgColor: "bg-blue-500" },
    { title: "KELOMPOK C", key: "C", bgColor: "bg-green-500" },
    { title: "KELOMPOK D", key: "D", bgColor: "bg-yellow-600" },
  ];

  return (
    <div className="min-h-screen bg-gray-100 flex flex-col items-center justify-center px-[10px] py-4">
      <div
        className="mb-4 p-2 rounded-lg text-sm font-semibold"
        style={{
          backgroundColor: isConnected ? "#4CAF50" : "#FF9800",
          color: "white",
        }}
      >
        Status: {statusMessage}
      </div>

      <div className="w-full h-[66.67vh]">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 h-full">
          {cardConfig.map((config) => (
            <Card
              key={config.key}
              title={config.title}
              value={data[config.key as keyof DashboardData] || "0"}
              bgColor={config.bgColor}
            />
          ))}
        </div>
      </div>
    </div>
  );
};

export default CardDashboard;
