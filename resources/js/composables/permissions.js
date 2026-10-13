import { usePage } from '@inertiajs/vue3'

export function usePermission() {
    const page = usePage()

    const hasRole = (name) => {
        return page.props.isSuperAdmin === true ||
            (page.props.roles ?? []).includes(name)
    }

    const hasPermissions = (name) => {
        return page.props.isSuperAdmin === true ||
            (page.props.permissions ?? []).includes(name)
    }

    return {
        hasRole,
        hasPermissions,
    }
}
