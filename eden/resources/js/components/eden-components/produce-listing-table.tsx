import {
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Paper,
} from '@mui/material';
import { useEffect, useState } from 'react';
import api from '@/lib/axios'

interface ProduceListing {
    id: bigint;
    produce: string;
    quantity: number;
    unit: string;
    price_per_unit: number;
    location: string;
    farmer_name: string;
}

export default function ProduceListingTable() {
    const [data, setData] = useState<ProduceListing[]>([]);

    useEffect(() => {
        api.get<ProduceListing[]>('/produce-listings').then((response) => {
            setData(response.data);
        });
    }, []);

    console.log('data start');
    console.log(data);
    console.log('data end');

    return (
        <TableContainer component={Paper}>
            <Table>
                <TableHead>
                    <TableRow>
                        <TableCell>Produce</TableCell>
                        <TableCell>Stock</TableCell>
                        <TableCell>Price Per Unit</TableCell>
                        <TableCell>Location</TableCell>
                        <TableCell>Listed by</TableCell>
                    </TableRow>
                </TableHead>

                <TableBody>
                    {data.map((datum) => (
                        <TableRow
                            key={datum.id}
                        >
                            <TableCell>{datum.produce}</TableCell>
                            <TableCell>{datum.quantity}{datum.unit}</TableCell>
                            <TableCell>{datum.price_per_unit}/{datum.unit}</TableCell>
                            <TableCell>{datum.location}</TableCell>
                            <TableCell>{datum.farmer_name}</TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </TableContainer>
    );
}
