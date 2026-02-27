import { ThemeProvider } from "@emotion/react";
import ReactDOM from "react-dom/client";
import App from "./AppRouting";
import edenTheme from './theme';
import '../css/app.css';

ReactDOM.createRoot(
  document.getElementById("app")!
).render(
  <ThemeProvider theme={edenTheme}>
    <App />
  </ThemeProvider>
);