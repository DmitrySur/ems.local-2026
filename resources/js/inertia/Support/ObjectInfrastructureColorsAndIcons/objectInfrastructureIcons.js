// Иконка зависит от type объекта инфраструктуры.
// name — название компонента из @tabler/icons-vue.
// rotate — поворот иконки в градусах.

export const objectInfrastructureIcons = {
    station: {
        name: 'IconGitCommit',
        rotate: 90,
    },

    driving: {
        name: 'IconCircuitSwitchClosed',
        rotate: 0,
    },

    brand_path: {
        name: 'IconGitMerge',
        rotate: -90,
    },

    end_road: {
        name: 'IconGitBranchDeleted',
        rotate: -90,
    },

    building: {
        name: 'IconBuilding ',
        rotate: 0,
    },
    // Иконка по умолчанию, если type пустой или неизвестный.
    default: {
        name: 'IconSquareRotated ',
        rotate: 0,
    },
}

export function getObjectInfrastructureIconConfig(type) {
    return objectInfrastructureIcons[type] ?? objectInfrastructureIcons.default
}
