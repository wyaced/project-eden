import { Box, Typography, Container } from '@mui/material';

export default function Footer() {
    return (
        <Box
            sx={{
                mt: 8,
                backgroundColor: '#000',
                borderTopLeftRadius: '2rem',
                borderTopRightRadius: '2rem',
                pt: 6,
                pb: 6,
                color: '#fff',
            }}
        >
            <Container maxWidth="lg">
                <Typography variant="h6" sx={{ mb: 2 }}>
                    EDEN
                </Typography>
                <Typography variant="body2">
                    &copy; Eden. All rights reserved.
                </Typography>
            </Container>
        </Box>
    );
}