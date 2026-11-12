import ApexCharts from 'apexcharts';

type ChartData = {
    labels?: string[];
    data?: number[];
    colors?: string[];
};

const numberFormatter = (value: number) => new Intl.NumberFormat('en-US').format(value);
const currencyFormatter = (value: number) => `$${numberFormatter(value)}`;

function dataOf(key: string): ChartData | null {
    const charts = (window as { dashboardCharts?: Record<string, ChartData> }).dashboardCharts;
    const data = charts?.[key];

    if (!data || !data.data || data.data.length === 0) {
        return null;
    }

    return data;
}

function renderDonut(elementId: string, key: string, fallbackPalette: string[]): void {
    const element = document.getElementById(elementId);
    const data = dataOf(key);

    if (!element || !data) {
        return;
    }

    new ApexCharts(element, {
        chart: {
            type: 'donut',
            height: 320,
            fontFamily: 'inherit',
            animations: { easing: 'easeinout', speed: 700 },
            toolbar: { show: false },
        },
        series: data.data,
        labels: data.labels,
        colors: data.colors ?? fallbackPalette,
        legend: { position: 'bottom', fontSize: '13px' },
        dataLabels: {
            enabled: true,
            formatter: (value: number, opts: { seriesIndex: number }) => {
                const total = data.data.reduce((sum, n) => sum + n, 0);
                const count = data.data[opts.seriesIndex];
                return `${numberFormatter(count)} (${Math.round(value)}%)`;
            },
        },
        tooltip: { y: { formatter: (value: number) => `${numberFormatter(value)} units` } },
        plotOptions: { pie: { donut: { size: '68%' } } },
    }).render();
}

function renderBar(elementId: string, key: string): void {
    const element = document.getElementById(elementId);
    const data = dataOf(key);

    if (!element || !data) {
        return;
    }

    new ApexCharts(element, {
        chart: { type: 'bar', height: 320, fontFamily: 'inherit', toolbar: { show: false }, animations: { easing: 'easeinout', speed: 700 } },
        series: [{ name: 'Leads', data: data.data }],
        labels: data.labels,
        colors: data.colors ?? ['#0F286F'],
        legend: { show: false },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '55%',
                dataLabels: { position: 'top' },
            },
        },
        dataLabels: {
            enabled: true,
            offsetY: -18,
            formatter: (value: number) => numberFormatter(value),
            style: { fontSize: '12px', fontWeight: 600 },
        },
        xaxis: { labels: { style: { fontSize: '12px' } } },
        yaxis: { labels: { formatter: (value: number) => numberFormatter(value), style: { fontSize: '12px' } } },
        tooltip: { y: { formatter: (value: number) => `${numberFormatter(value)} leads` } },
        grid: { borderColor: 'rgba(15, 40, 111, 0.08)' },
    }).render();
}

function renderLine(elementId: string, key: string): void {
    const element = document.getElementById(elementId);
    const data = dataOf(key);

    if (!element || !data) {
        return;
    }

    new ApexCharts(element, {
        chart: { type: 'line', height: 320, fontFamily: 'inherit', toolbar: { show: false }, animations: { easing: 'easeinout', speed: 700 } },
        series: [{ name: 'Sales', data: data.data }],
        labels: data.labels,
        colors: ['#2563EB'],
        stroke: { curve: 'smooth', width: 3 },
        fill: { gradient: { enabled: true, opacityFrom: 0.35, opacityTo: 0.04, stops: [0, 90] } },
        markers: { size: 4, strokeWidth: 0, hover: { size: 6 } },
        dataLabels: { enabled: false },
        xaxis: { labels: { style: { fontSize: '12px' } } },
        yaxis: { labels: { formatter: (value: number) => numberFormatter(value), style: { fontSize: '12px' } } },
        tooltip: { y: { formatter: currencyFormatter } },
        grid: { borderColor: 'rgba(15, 40, 111, 0.08)' },
    }).render();
}

function renderProgress(elementId: string, key: string): void {
    const element = document.getElementById(elementId);
    const data = dataOf(key);

    if (!element || !data) {
        return;
    }

    new ApexCharts(element, {
        chart: { type: 'bar', height: 320, fontFamily: 'inherit', toolbar: { show: false }, animations: { easing: 'easeinout', speed: 700 } },
        series: [{ name: 'Completion', data: data.data }],
        labels: data.labels,
        colors: ['#0F286F'],
        legend: { show: false },
        plotOptions: { bar: { borderRadius: 6, horizontal: true, barHeight: '55%' } },
        dataLabels: { enabled: true, formatter: (value: number) => `${Math.round(value)}%`, style: { fontSize: '12px', fontWeight: 600 } },
        xaxis: { min: 0, max: 100, labels: { formatter: (value: number) => `${Math.round(value)}%`, style: { fontSize: '12px' } } },
        tooltip: { y: { formatter: (value: number) => `${Math.round(value)}%` } },
        grid: { borderColor: 'rgba(15, 40, 111, 0.08)' },
    }).render();
}

renderDonut('unitStatusChart', 'unitStatus', ['#0F286F', '#3B82F6', '#93C5FD', '#60A5FA']);
renderBar('leadPipelineChart', 'leadPipeline');
renderLine('salesTrendChart', 'salesTrend');
renderDonut('leadSourcesChart', 'leadSources', ['#0F286F', '#2563EB', '#3B82F6', '#60A5FA', '#93C5FD', '#BFDBFE']);
renderProgress('projectProgressChart', 'projectProgress');