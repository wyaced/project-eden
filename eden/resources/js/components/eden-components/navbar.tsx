import {
    AppBar,
    Box,
    Toolbar,
    Typography,
    Container,
    Button,
} from '@mui/material';
import { useNavigate } from 'react-router-dom';

type PageArr = [string, string];

interface NavBarProps {
    pages: PageArr[];
}

export default function NavBar({ pages }: NavBarProps) {
    const navigate = useNavigate();

    function redirectToPage(page: string) {
        navigate(page);
    }

    return (
        <AppBar position="static">
            <Container maxWidth="xl">
                <Toolbar disableGutters>
                    <Typography
                        variant="h5"
                        noWrap
                        component="a"
                        href="/"
                        sx={{
                            mr: 2,
                            display: 'flex',
                            fontFamily: 'monospace',
                            fontWeight: 700,
                            letterSpacing: '.2rem',
                            color: 'inherit',
                            textDecoration: 'none',
                        }}
                    >
                        EDEN
                    </Typography>

                    <Box
                        sx={{
                            flexGrow: 1,
                            display: { xs: 'none', md: 'flex' },
                        }}
                    >
                        {pages.map((page) => (
                            <Button
                                key={page[0]}
                                onClick={() => redirectToPage(page[1])}
                                sx={{ my: 2, color: 'white', display: 'block' }}
                            >
                                {page[0]}
                            </Button>
                        ))}
                    </Box>
                </Toolbar>
            </Container>
        </AppBar>
    );
}
