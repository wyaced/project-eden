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
import ProduceListingTable from '@/components/eden-components/produce-listing-table';
import api from '@/lib/axios';

export default function Listings() {
    const [produceNames, setProduceNames] = useState<string[]>([]);
    const [produce, setProduce] = useState('');

    const handleChange = (event: SelectChangeEvent) => {
        setProduce(event.target.value as string);
    };

    useEffect(() => {
        api.get<string[]>('/produce-names').then((response) => {
            setProduceNames(response.data);
        });
    }, []);

    console.log(produceNames);

    return (
        <div className="p-2">
            <Typography variant="h5">Listings</Typography>
            <FormControl className='w-full m-2 p-2' component={Paper}>
                <InputLabel id="demo-simple-select-label">
                    Select Produce
                </InputLabel>
                <Select
                    labelId="demo-simple-select-label"
                    id="demo-simple-select"
                    value={produce}
                    label="Select Produce"
                    onChange={handleChange}
                >
                    {produceNames.map((produce) => (
                        <MenuItem value={produce}>{produce}</MenuItem>
                    ))}
                </Select>
            </FormControl>
            <Paper elevation={6} className="m-2 flex gap-2 p-2">
                <div style={{ width: '50%', maxWidth: 1000 }}>
                    <Typography variant="h6" gutterBottom>
                        Supply Movements
                    </Typography>
                    <MarketMovementsChart
                        movementType="supply"
                        movementUnit="kg"
                        produce={produce}
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
                    />
                </div>
            </Paper>
            <div className="m-2 flex justify-center">
                <ProduceListingTable 
                    produce={produce}
                />
            </div>
        </div>
    );
}
