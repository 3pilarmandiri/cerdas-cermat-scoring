"use client";

import React, { useState, useEffect } from "react";
import io, { Socket } from "socket.io-client";

const WS_URL = process.env.NEXT_PUBLIC_WS_URL || "http://localhost:3003";

interface CardProps {
  title: string;
  subtitle: string;
  value: string;
  bgColor: string;
}

interface DashboardData {
  pertandingan: string;
  skor: Record<"A" | "B" | "C" | "D", string>;
  kelompok: Record<"A" | "B" | "C" | "D", string>;
}

// --- Komponen Card ---
const Card: React.FC<CardProps> = ({ title, subtitle, value, bgColor }) => {
  const [animate, setAnimate] = useState(false);

  useEffect(() => {
    setAnimate(true);
    const timeout = setTimeout(() => setAnimate(false), 500);
    return () => clearTimeout(timeout);
  }, [value]);

  return (
    <div
      className={`rounded-xl shadow-lg transition-transform duration-300 h-full flex flex-col ${bgColor}`}
    >
      {/* Bagian Judul */}
      <div className="bg-white/30 rounded-t-xl py-6 text-center flex flex-col justify-center items-center">
        <h2 className="text-3xl font-extrabold text-white tracking-wide">
          {title}
        </h2>
        <p className="text-lg font-semibold text-white mt-1 drop-shadow-md">
          {subtitle || "-"}
        </p>
      </div>

      {/* Bagian Skor */}
      <div className="flex flex-col justify-center items-center bg-white/90 rounded-b-xl flex-grow py-10">
        <div
          className={`border-8 border-gray-700 rounded-full w-64 h-64 flex items-center justify-center 
            transition-transform duration-500 ease-in-out shadow-lg ${
              animate ? "scale-110" : "scale-100"
            }`}
        >
          <span
            className={`text-[8rem] font-extrabold text-gray-800 transition-transform duration-500 ${
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
    pertandingan: "Memuat Pertandingan...",
    skor: { A: "0", B: "0", C: "0", D: "0" },
    kelompok: { A: "-", B: "-", C: "-", D: "-" },
  });

  const [isConnected, setIsConnected] = useState(false);
  const [statusMessage, setStatusMessage] = useState("Menghubungkan...");
  const [socket, setSocket] = useState<Socket | null>(null);

  const fetchLatestData = async () => {
    try {
      const response = await fetch(`${WS_URL}/latest-data`);
      if (!response.ok) throw new Error("Gagal mengambil data terakhir");
      const latestData = await response.json();
      setData((prev) => ({
        ...prev,
        ...latestData,
      }));
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
    <div className="min-h-screen bg-gradient-to-b from-gray-100 to-gray-300 flex flex-col items-center justify-between py-4 px-[10px]">
      {/* Header */}
      <div className="text-center mb-6">
        <h1 className="text-5xl font-extrabold text-gray-900 drop-shadow-lg mb-2">
          CERDAS CERMAT KARTU KSB MAJU
        </h1>
        <hr className="w-1/2 mx-auto border-gray-400 mb-4" />
        <h2 className="text-4xl font-extrabold text-gray-800 drop-shadow-lg mb-2">
          {data.pertandingan}
        </h2>
        <div className="w-32 h-1 bg-gray-700 mx-auto rounded-full shadow-md"></div>
      </div>

      {/* Grid Skor */}
      <div className="w-full flex-1 flex items-center">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
          {cardConfig.map((config) => (
            <Card
              key={config.key}
              title={config.title}
              subtitle={data.kelompok[config.key as keyof typeof data.kelompok]}
              value={data.skor[config.key as keyof typeof data.skor] || "0"}
              bgColor={config.bgColor}
            />
          ))}
        </div>
      </div>

      {/* Status Koneksi */}
      <div className="mt-6 mb-2">
        <div
          className={`px-4 py-2 rounded-lg text-sm font-semibold shadow-md ${
            isConnected ? "bg-green-600" : "bg-orange-500"
          } text-white`}
        >
          Status: {statusMessage}
        </div>
      </div>
    </div>
  );
};

export default CardDashboard;
