import { createTheme } from '@mui/material/styles';

const edenTheme = createTheme({
    palette: {
        background: {
            default: '#255527',
            paper: '#c7c9e1',
        },
        primary: {
            light: '#5d945f',
            main: '#357a38',
            dark: '#255527',
            contrastText: '#fff',
        },
        secondary: {
            light: '#255355',
            main: '#35777a',
            dark: '#5d9294',
            contrastText: '#000',
        },
    },
});

export default edenTheme;
