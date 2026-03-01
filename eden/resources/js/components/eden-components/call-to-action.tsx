import { Box, Typography, Button } from '@mui/material';

export default function CallToAction() {
    return (
        <Box
            sx={{
                pt: 20,
                pb: 12,
                px: { xs: 4, md: 10 },
                backgroundColor: '#fff',
                color: '#000',
                textAlign: 'center',
            }}
        >
            <Box
                sx={{
                    maxWidth: 800,
                    mx: 'auto',
                }}
            >
                <Typography
                    variant="h3"
                    sx={{
                        fontWeight: 800,
                        mb: 4,
                        letterSpacing: 1,
                    }}
                >
                    Protect the hands that feed us.
                </Typography>

                <Typography
                    variant="h6"
                    sx={{
                        mb: 6,
                        opacity: 0.9,
                        lineHeight: 1.8,
                    }}
                >
                    Balance the harvest. Empower farmers with data.
                    <br />
                    Reduce waste. Increase income. Create fairer markets.
                </Typography>
            </Box>
        </Box>
    );
}