import { Typography } from '@mui/material';
import PriceMovements from '@/components/eden-components/price-movements-chart';
import ProduceListingTable from '@/components/eden-components/produce-listing-table';
import SupplyMovements from '@/components/eden-components/supply-movements-chart';

export default function Listings() {
    return (
        <div className="p-2">
            <Typography variant="h5">Listing!</Typography>
            <div className='flex gap-2'>
                <SupplyMovements />
                <PriceMovements />
            </div>
            <ProduceListingTable />
        </div>
    );
}
