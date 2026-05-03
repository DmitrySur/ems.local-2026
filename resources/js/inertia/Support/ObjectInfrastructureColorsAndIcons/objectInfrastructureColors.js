const objectInfrastructureColors = {
    '1-СЛ': '#EF1E25',
    '2-ЗЛ': '#029A55',
    '3-АПЛ/ФЛ': '#0070BA',
    '4-КЛ': '#8D5B2D',
    '5-КРЛ': '#ED7121',
    '6-ТКЛ': '#800080',
    '7-КСЛ': '#FFD112',
    '8-СТЛ/БЛ': '#ACADAF',
    '9-ЛДЛ': '#B1D332',
    '10-БКЛ': '#82C0C0',
    '11-НЛ': '#DE64A1',
    'НО': '#1677ff',
    'МЦК': '#EF1E25',
    '13-ТЛ': '#166c58',
    default: '#8c8c8c',
};

export function getObjectInfrastructureColor(shortName) {
    return objectInfrastructureColors[shortName] ?? objectInfrastructureColors.default
}
