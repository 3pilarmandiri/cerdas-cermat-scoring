// app/page.tsx
import CardDashboard from '@/components/CardDashboard';

// Komponen page ini adalah Server Component secara default
export default function Home() {
  return (
    // Render Client Component (CardDashboard) di sini
    <main>
      <CardDashboard />
    </main>
  );
}