export const objectInfrastructurePrefixes = {
    station: 'ст.',
    driving: 'перегон',
    end_road: '',
    brand_path: 'ветка',
    building: '',
    default: '',
}

export function getObjectInfrastructurePrefix(type) {
    return objectInfrastructurePrefixes[type] ?? objectInfrastructurePrefixes.default
}
