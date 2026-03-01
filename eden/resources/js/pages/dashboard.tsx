import {
    Typography,
    FormControl,
    InputLabel,
    MenuItem,
    Select,
    Paper,
} from '@mui/material';
import type { SelectChangeEvent } from '@mui/material/Select';
import { useEffect, useState } from 'react';
import MarketMovementsChart from '@/components/eden-components/market-movements-chart';
import api from '@/lib/axios';

export default function Dashboard() {
    const [produceNames, setProduceNames] = useState<string[]>([]);
    const [locations, setLocations] = useState<string[]>([]);
    const [produce, setProduce] = useState('');
    const [location, setLocation] = useState('all');

    const handleProduceChange = (event: SelectChangeEvent) => {
        setProduce(event.target.value);
    };

    const handleLocationChange = (event: SelectChangeEvent) => {
        setLocation(event.target.value);
    };

    useEffect(() => {
        api.get<string[]>('/produce-names').then((response) => {
            setProduceNames(response.data);
        });

        api.get<string[]>('/location-names').then((response) => {
            setLocations(['all', ...response.data]);
        });
    }, []);

    console.log(produceNames);
    console.log(locations);

    return (
        <div className="p-2">
            <Typography variant="h5">Dashboard</Typography>
            <div className="flex w-full justify-center gap-2">
                <FormControl
                    className="m-2 p-2"
                    style={{ width: '49%' }}
                    component={Paper}
                >
                    <InputLabel id="produce-select-label">
                        Select Produce
                    </InputLabel>
                    <Select
                        labelId="produce-select-label"
                        id="produce-select"
                        value={produce}
                        label="Select Produce"
                        onChange={handleProduceChange}
                    >
                        {produceNames.map((produce) => (
                            <MenuItem key={produce} value={produce}>
                                {produce}
                            </MenuItem>
                        ))}
                    </Select>
                </FormControl>
                <FormControl
                    className="m-2 p-2"
                    style={{ width: '49%' }}
                    component={Paper}
                >
                    <InputLabel id="location-select-label">
                        Select Location
                    </InputLabel>
                    <Select
                        labelId="location-select-label"
                        id="location-select"
                        value={location}
                        label="Select Location"
                        onChange={handleLocationChange}
                    >
                        {locations.map((location) => (
                            <MenuItem key={location} value={location}>
                                {location}
                            </MenuItem>
                        ))}
                    </Select>
                </FormControl>
            </div>
            <Paper elevation={6} className="m-2 flex gap-2 p-2">
                <div style={{ width: '50%', maxWidth: 1000 }}>
                    <Typography variant="h6" gutterBottom>
                        Supply Movements
                    </Typography>
                    <MarketMovementsChart
                        movementType="supply"
                        movementUnit="kg"
                        produce={produce}
                        produceLocation={location}
                    />
                </div>
                <div style={{ width: '50%', maxWidth: 1000 }}>
                    <Typography variant="h6" gutterBottom>
                        Price Movements
                    </Typography>
                    <MarketMovementsChart
                        movementType="price"
                        movementUnit="PHP"
                        produce={produce}
                        produceLocation={location}
                    />
                </div>
            </Paper>
        </div>
    );
}
