import { useEffect, useState } from 'react';

import { LineChart, Line, XAxis, YAxis, CartesianGrid } from 'recharts';


import api from '@/lib/axios';

interface SupplyData {
    timestamp: number;
    [location: string]: number | undefined;
}

export default function SupplyMovements() {
    const [supplyData, setSupplyData] = useState<SupplyData[]>([]);
    const [locations, setLocations] = useState<string[]>([]);
    const COLORS = [
        '#8884d8',
        '#82ca9d',
        '#ff7300',
        '#ff0000',
        '#00c49f',
        '#0088fe',
        '#a83279',
        '#ffc658',
    ];

    useEffect(() => {
        api.get<SupplyData[]>('/market-movement-records/supply/kamote').then(
            (response) => {
                setSupplyData(response.data);

                const locations = Array.from(
                    new Set(
                        response.data.flatMap((obj) =>
                            Object.keys(obj).filter(
                                (key) => key !== 'timestamp',
                            ),
                        ),
                    ),
                );
                setLocations(locations);
            },
        );
    }, []);

    return (
            <LineChart
                style={{ width: '50%', aspectRatio: 1.7, maxWidth: 1000 }}
                responsive
                data={supplyData}
            >
                <CartesianGrid />
                <XAxis
                    dataKey="timestamp"
                    type="number"
                    domain={['auto', 'auto']}
                    tickFormatter={(value) =>
                        new Date(value).toLocaleTimeString()
                    }
                />
                <YAxis
                    type="number"
                    domain={['auto', 'auto']}
                />

                {locations.map((location, index) => (
                    <Line
                        key={location}
                        dataKey={location}
                        stroke={COLORS[index % COLORS.length]}
                    />
                ))}
            </LineChart>
    );
}
