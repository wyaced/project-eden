import { Typography } from '@mui/material';
import ProduceListingTable from '@/components/eden-components/produce-listing-table';

export default function Welcome() {
    return (
        <div className='p-2'>
            <Typography variant="h3">Welcome to Eden!</Typography>
            <ProduceListingTable />
        </div>
    );
}
