import { useEffect, useState } from 'react';

import {
    LineChart,
    Line,
    XAxis,
    YAxis,
    CartesianGrid,
    Legend,
    Tooltip,
} from 'recharts';

import api from '@/lib/axios';

interface SupplyData {
    timestamp: number;
    [location: string]: number | undefined;
}

interface MovementChartProps {
    movementType: string;
    movementUnit: string;
    produce: string;
    produceLocation: string;
}

export default function MarketMovementsChart({
    movementType,
    movementUnit,
    produce,
    produceLocation,
}: MovementChartProps) {
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
        api.get<SupplyData[]>(
            '/market-movement-records/' + movementType + '/' + produce + '/' + produceLocation,
        ).then((response) => {
            setSupplyData(response.data);

            const locations = Array.from(
                new Set(
                    response.data.flatMap((obj) =>
                        Object.keys(obj).filter((key) => key !== 'timestamp'),
                    ),
                ),
            );
            setLocations(locations);
        });
    }, [movementType, movementUnit, produce, produceLocation]);

    const YAxisLabel = movementType + '(' + movementUnit + ')';

    return (
        <LineChart
            style={{ width: '100%', aspectRatio: 2.75, maxWidth: 1000 }}
            responsive
            data={supplyData}
        >
            <CartesianGrid />
            <XAxis
                dataKey="timestamp"
                type="number"
                domain={['auto', 'auto']}
                tickFormatter={(value) => new Date(value).toLocaleTimeString()}
            />
            <YAxis
                type="number"
                domain={['auto', 'auto']}
                label={{
                    value: YAxisLabel,
                    position: 'insideLeft',
                    angle: -90,
                }}
            />

            {locations.map((location, index) => (
                <Line
                    key={location}
                    dataKey={location}
                    stroke={COLORS[index % COLORS.length]}
                    strokeWidth={2}
                    type="monotone"
                />
            ))}
            <Tooltip
                content={({ active, payload, label }) => {
                    if (active && payload && payload.length) {
                        return (
                            <div
                                style={{
                                    background: 'white',
                                    padding: 10,
                                    border: '1px solid #ccc',
                                }}
                            >
                                <strong>
                                    Time:{' '}
                                    {label !== undefined
                                        ? new Date(label).toLocaleTimeString()
                                        : 'N/A'}
                                </strong>
                                <ul style={{ paddingLeft: 0 }}>
                                    {payload.map((p) => (
                                        <li
                                            key={p.dataKey}
                                            style={{
                                                color: p.color,
                                                listStyle: 'none',
                                            }}
                                        >
                                            {p.dataKey}: {p.value}{' '}
                                            {movementUnit}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        );
                    }
                    return null;
                }}
            />
            <Legend />
        </LineChart>
    );
}
