// import { Card, CardContent, Typography, Grid} from "@mui/material";
// import {
//   LineChart,
//   Line,
//   XAxis,
//   YAxis,
//   Tooltip,
//   ResponsiveContainer,
// } from "recharts";
// import { useEffect, useState } from "react";
// import api from "@/lib/axios";

// const data = [
//   { day: "Mon", price: 20 },
//   { day: "Tue", price: 35 },
//   { day: "Wed", price: 28 },
//   { day: "Thu", price: 40 },
// ];

// export default function DashboardTest() {
//   return (
//     <Grid container spacing={2}>
      
//       {/* MUI Card Test */}
//       <Grid size={{ xs: 12, md: 6 }}>
//         <Card>
//           <CardContent>
//             <Typography variant="h6">
//               Eden Market Price Test
//             </Typography>
//             <Typography>
//               If you see this card, MUI works ✅
//             </Typography>
//           </CardContent>
//         </Card>
//       </Grid>

//       {/* Recharts Test */}
//       <Grid size={{ xs: 12, md: 6 }}>
//         <Card>
//           <CardContent>
//             <Typography variant="h6">
//               Price Trend Test
//             </Typography>

//             <ResponsiveContainer width="100%" height={300}>
//               <LineChart data={data}>
//                 <XAxis dataKey="day" />
//                 <YAxis />
//                 <Tooltip />
//                 <Line
//                   type="monotone"
//                   dataKey="price"
//                   stroke="#1976d2"
//                 />
//               </LineChart>
//             </ResponsiveContainer>

//           </CardContent>
//         </Card>
//       </Grid>

//     </Grid>
//   );
// }
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

export default function DashboardTest() {

  const [data, setData] = useState<MarketData[]>([]);
  const [loading, setLoading] = useState(true);

  // Axios → Laravel API
  useEffect(() => {
    api.get<MarketData[]>("/market-test")
      .then((res) => {
        setData(res.data);
        setLoading(false);
      })
      .catch((err) => {
        console.error(err);
        setLoading(false);
      });
  }, []);

  console.log(data);

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