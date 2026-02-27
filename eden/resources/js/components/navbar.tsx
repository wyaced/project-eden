import * as React from "react";
import {
  AppBar,
  Toolbar,
  Typography,
  Box,
  Button,
  InputBase,
} from "@mui/material";
import SearchIcon from "@mui/icons-material/Search";
import { styled, alpha } from "@mui/material/styles";

const Search = styled("div")(({ theme }) => ({
  position: "relative",
  borderRadius: 30,
  backgroundColor: alpha("#ffffff", 0.15),
  "&:hover": {
    backgroundColor: alpha("#ffffff", 0.25),
  },
  marginRight: theme.spacing(2),
  width: "250px",
}));

const SearchIconWrapper = styled("div")({
  padding: "0 16px",
  height: "100%",
  position: "absolute",
  pointerEvents: "none",
  display: "flex",
  alignItems: "center",
  justifyContent: "center",
});

const StyledInputBase = styled(InputBase)({
  color: "inherit",
  paddingLeft: "48px",
  width: "100%",
});

export default function AgricultureNavbar() {
  return (
    <AppBar
      position="static"
      elevation={0}
      sx={{
        background: "linear-gradient(90deg, #2e7d32, #66bb6a)",
        paddingY: 1,
      }}
    >
      <Toolbar sx={{ display: "flex", justifyContent: "space-between" }}>
        
        {/* LEFT - Logo */}
        <Typography variant="h6" sx={{ fontWeight: 600 }}>
           Project: Eden
        </Typography>

        {/* CENTER - Navigation */}
        <Box sx={{ display: "flex", gap: 4, position: "relative" }}>
          <Typography sx={{ cursor: "pointer" }}>Market</Typography>
          <Typography sx={{ cursor: "pointer" }}>Produce</Typography>
          <Typography sx={{ cursor: "pointer" }}>About</Typography>
        </Box>
      
      </Toolbar>
    </AppBar>
  );
}