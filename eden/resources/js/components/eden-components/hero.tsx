import { Link } from 'react-router-dom';
import { Box, Typography, Button } from '@mui/material';

export default function Hero() {
    return (
        <Box
            sx={{
                height: '100vh',
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'flex-start',
                alignItems: 'flex-start',
                paddingTop: { xs: '15%', md: '10%' },
                paddingLeft: { xs: 4, md: 10 },
                backgroundImage: "url('/hero-bg.png')",
                backgroundSize: 'cover',
                backgroundRepeat: 'no-repeat',
                backgroundPosition: 'center',
                color: '#fff',
                position: 'relative',
            }}
        >
            <Box
                sx={{
                    position: 'absolute',
                    inset: 0,
                    backgroundColor: 'rgba(0,0,0,0.3)',
                }}
            />
            <Box sx={{ position: 'relative', zIndex: 1 }}>
                <Typography variant="h2" sx={{ mb: 3, fontWeight: 700 }}>
                    Welcome to Eden
                </Typography>
                <Typography variant="h5" sx={{ mb: 4, color: '#94b995' }}>
                    Because Every Harvest Deserves A Market;<br />
                    Harvest Smart. Sell Smarter.
                </Typography>
                <Button
                    component={Link}
                    to="/listings"
                    variant="contained"
                    sx={{
                        backgroundColor: '#ffd700',
                        color: '#000',
                        fontWeight: 700,
                        '&:hover': { backgroundColor: '#ffc700' },
                    }}
                >
                    See Listings
                </Button>
            </Box>
        </Box>
    );
}