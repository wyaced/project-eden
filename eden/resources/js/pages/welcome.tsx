// MUI
import {
  Card,
  CardContent,
  Typography,
  Grid,
  CircularProgress,
} from "@mui/material";

import { useEffect, useState } from "react";

// Recharts
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  Tooltip,
  ResponsiveContainer,
} from "recharts";

import api from "@/lib/axios";

interface MarketData {
  day: string;
  price: number;
}

export default function Welcome() {

  const [data, setData] = useState<MarketData[]>([]);
  const [loading, setLoading] = useState(true);

  // Axios → Laravel API
  useEffect(() => {
    api.get<MarketData[]>("/api/market-test")
      .then((res) => {
        setData(res.data);
        setLoading(false);
      })
      .catch((err) => {
        console.error(err);
        setLoading(false);
      });
  }, []);

  return (
    <Grid container spacing={3}>

      {/* MUI CARD */}
      <Grid size={{ xs: 12 }}>
        <Card>
          <CardContent>

            <Typography variant="h5" gutterBottom>
              Eden Market Dashboard Test
            </Typography>

            {loading ? (

              <CircularProgress />

            ) : (

              <ResponsiveContainer width="100%" height={300}>

                {/* RECHARTS GRAPH */}
                <LineChart data={data}>

                  <XAxis dataKey="day" />

                  <YAxis />

                  <Tooltip />

                  <Line
                    type="monotone"
                    dataKey="price"
                    stroke="#2e7d32"
                    strokeWidth={3}
                  />

                </LineChart>

              </ResponsiveContainer>

            )}

          </CardContent>
        </Card>
      </Grid>

    </Grid>
  );
}