import {
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Paper,
    TablePagination,
} from '@mui/material';
import { useEffect, useState } from 'react';
import api from '@/lib/axios';

interface ProduceListing {
    id: bigint;
    produce: string;
    quantity: number;
    unit: string;
    price_per_unit: number;
    location: string;
    farmer_name: string;
}

interface ProduceListingProps {
    produce: string;
}

export default function ProduceListingTable({ produce }: ProduceListingProps) {
    const [data, setData] = useState<ProduceListing[]>([]);
    const [page, setPage] = useState(0);
    const [rowsPerPage, setRowsPerPage] = useState(5);

    useEffect(() => {
        api.get<ProduceListing[]>('/produce-listings/' + produce).then((response) => {
            setData(response.data);
        });
    }, [produce]);

    // handle page change
    const handleChangePage = (event: unknown, newPage: number) => {
        setPage(newPage);
    };

    // handle rows per page change
    const handleChangeRowsPerPage = (event: React.ChangeEvent<HTMLInputElement>) => {
        setRowsPerPage(parseInt(event.target.value, 10));
        setPage(0); // reset to first page
    };

    // slice data for current page
    const paginatedData = data.slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage);

    return (
        <Paper elevation={6} style={{ width: '100%', borderRadius: '8px', overflow: 'hidden' }}>
            <TableContainer>
                <Table size="small">
                    <TableHead>
                        <TableRow sx={{ backgroundColor: 'green', color: 'white' }}>
                            <TableCell sx={{ color: 'white' }}>ListingID</TableCell>
                            <TableCell sx={{ color: 'white' }}>Produce</TableCell>
                            <TableCell sx={{ color: 'white' }}>Stock</TableCell>
                            <TableCell sx={{ color: 'white' }}>Price Per Unit</TableCell>
                            <TableCell sx={{ color: 'white' }}>Location</TableCell>
                            <TableCell sx={{ color: 'white' }}>Listed by</TableCell>
                        </TableRow>
                    </TableHead>

                    <TableBody>
                        {paginatedData.map((datum) => (
                            <TableRow key={datum.id}>
                                <TableCell>{datum.id}</TableCell>
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

            {/* Pagination */}
            <TablePagination
                component="div"
                count={data.length}
                page={page}
                onPageChange={handleChangePage}
                rowsPerPage={rowsPerPage}
                onRowsPerPageChange={handleChangeRowsPerPage}
                rowsPerPageOptions={[5, 10, 25]}
            />
        </Paper>
    );
}