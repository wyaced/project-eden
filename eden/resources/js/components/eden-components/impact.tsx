import { Box, Typography } from '@mui/material';

export default function Impact() {
    return (
        <Box
            sx={{
                pt: 12,
                pb: 18,
                px: { xs: 4, md: 10 },
                background: "linear-gradient(180deg, #357a38 0%, #5d945f 100%)",
                color: '#fff',
            }}
        >
            <Typography
                variant="h3"
                sx={{ mb: 6, fontWeight: 700 }}
            >
                Impact
            </Typography>

            <Box
                sx={{
                    display: 'flex',
                    flexDirection: { xs: 'column', md: 'row' },
                    gap: 6,
                }}
            >
                <Box sx={{ flex: 1 }}>
                    <Typography variant="h6" sx={{ mb: 1, fontWeight: 700 }}>
                        Higher Farmer Income
                    </Typography>
                    <Typography variant="body1">
                        Farmers can sell where demand is strongest, reducing losses from local oversupply.
                    </Typography>
                </Box>

                <Box sx={{ flex: 1 }}>
                    <Typography variant="h6" sx={{ mb: 1, fontWeight: 700 }}>
                        Reduced Food Waste
                    </Typography>
                    <Typography variant="body1">
                        Surplus produce is redirected to areas that need it before it spoils.
                    </Typography>
                </Box>

                <Box sx={{ flex: 1 }}>
                    <Typography variant="h6" sx={{ mb: 1, fontWeight: 700 }}>
                        Inclusive Access
                    </Typography>
                    <Typography variant="body1">
                        SMS-based access ensures even farmers without smartphones can participate.
                    </Typography>
                </Box>

                <Box sx={{ flex: 1 }}>
                    <Typography variant="h6" sx={{ mb: 1, fontWeight: 700 }}>
                        Fairer Prices
                    </Typography>
                    <Typography variant="body1">
                        Efficient distribution benefits both farmers and consumers with more stable pricing.
                    </Typography>
                </Box>
            </Box>
        </Box>
    );
}