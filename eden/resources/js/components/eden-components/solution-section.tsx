import { Box, Typography } from '@mui/material';
import PriceMovements from './price-movements-chart';
import SupplyMovements from './supply-movements-chart';

export default function SolutionSection() {
    return (
        <Box
            sx={{
                py: 12,
                px: { xs: 4, md: 10 },
                background: "linear-gradient(180deg, #255527 0%, #357a38 100%)",
                color: '#fff',
            }}
        >
            <Typography
                variant="h3"
                sx={{
                    mb: 6,
                    fontWeight: 700,
                    textAlign: { xs: 'center', md: 'left' },
                }}
            >
                The Solution
            </Typography>

            <Box
                sx={{
                    display: 'flex',
                    flexDirection: { xs: 'column', md: 'row' },
                    gap: 6,
                    justifyContent: 'space-between',
                }}
            >
                <Box sx={{ flex: 1 }}>
                    <Typography variant="h6" sx={{ mb: 1, fontWeight: 700 }}>
                        Smart Marketplace
                    </Typography>
                    <Typography variant="body1">
                        Farmers can post produce listings, view real-time demand insights,
                        and connect directly with buyers or other farms.
                    </Typography>
                </Box>

                <Box sx={{ flex: 1 }}>
                    <Typography variant="h6" sx={{ mb: 1, fontWeight: 700 }}>
                        Real-Time Market Intelligence
                    </Typography>
                    <Typography variant="body1">
                        Eden detects surplus and scarcity using supply-demand analysis,
                        helping farmers know where to sell and when to act.
                    </Typography>
                </Box>

                <Box sx={{ flex: 1 }}>
                    <Typography variant="h6" sx={{ mb: 1, fontWeight: 700 }}>
                        Accessible to All Farmers
                    </Typography>
                    <Typography variant="body1">
                        Through SMS-based access, farmers without high-speed internet
                        can still receive market data and submit listings.
                    </Typography>
                </Box>
            </Box>
        </Box>
    );
}