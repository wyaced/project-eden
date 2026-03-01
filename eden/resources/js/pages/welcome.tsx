import { Typography } from '@mui/material';

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
  <Grid container spacing={0}>
   
      <Card sx={{ borderRadius: 0 }}>
        <CardContent>
          <Typography variant="h5" gutterBottom>
            Eden Market Dashboard Test
          </Typography>

          {loading ? (
            <CircularProgress />
          ) : (
            <ResponsiveContainer width="100%" height={300}>
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

);

}
