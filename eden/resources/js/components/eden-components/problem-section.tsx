import { Box, Link, Typography } from '@mui/material';
import { useEffect } from 'react';

export default function ProblemSection() {
    return (
        <Box
            sx={{
                py: 12,
                px: { xs: 4, md: 10 },
                background: "linear-gradient(180deg, #000000 70%, #255527 100%)",
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
                The Problem
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
                        Massive Waste
                    </Typography>
                    <Typography variant="body1">
                        30-40% of produce is lost after harvest. Example: mangoes rot in Guimaras while Manila faces shortages.
                    </Typography>
                </Box>

                <Box sx={{ flex: 1 }}>
                    <Typography variant="h6" sx={{ mb: 1, fontWeight: 700 }}>
                        Information Gap
                    </Typography>
                    <Typography variant="body1">
                        Farmers often don’t know which markets are "hot" or have surpluses, forcing low prices or wasted crops.
                    </Typography>
                </Box>

                <Box sx={{ flex: 1 }}>
                    <Typography variant="h6" sx={{ mb: 1, fontWeight: 700 }}>
                        Tech Barrier
                    </Typography>
                    <Typography variant="body1">
                        Many rural farmers lack high-speed internet, keeping them disconnected from real-time market needs.
                    </Typography>
                </Box>
            </Box>
            <Box sx={{ mt: 6, textAlign: { xs: 'center', md: 'left' } }}>
                <Typography variant="body1" sx={{ fontStyle: 'italic' }}>
                    Watch a related news video:{" "}
                    <Link
                        href="https://www.tiktok.com/@gmanews/video/7609893409398066453"
                        target="_blank"
                        rel="noopener noreferrer"
                        sx={{ color: '#ffd700', fontWeight: 700 }}
                    >
                        TikTok Video
                    </Link>
                </Typography>
            </Box>
        </Box>
    );
}