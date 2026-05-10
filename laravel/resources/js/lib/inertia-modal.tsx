import { http, router, usePage } from '@inertiajs/react';
import React, { useEffect, useState } from 'react';

type ModalData = {
    component: string;
    redirectURL: string;
    props: Record<string, unknown>;
    key: string;
};

type ModalComponent = React.ComponentType<Record<string, unknown>>;
type ResolveComponent = (name: string) => Promise<{ default: ModalComponent }>;

let _resolve: ResolveComponent | null = null;
let _currentPage: { component: string; props: Record<string, unknown> } | null = null;

const preserveKeys = [
    'scrollProps',
    'mergeProps',
    'prependProps',
    'deepMergeProps',
    'matchPropsOn',
    'deferredProps',
    'sharedProps',
    'onceProps',
] as const;

function mergePageData(current: unknown, incoming: unknown): unknown {
    if (Array.isArray(current) || Array.isArray(incoming)) {
        return [...new Set([...(current as unknown[] ?? []), ...(incoming as unknown[] ?? [])])];
    }

    return { ...JSON.parse(JSON.stringify(current ?? {})), ...(incoming ?? {}) };
}

/**
 * Call once before createInertiaApp. Registers the HTTP interceptor that
 * preserves the backdrop page when a modal response arrives.
 */
export function setupInertiaModal(resolve: ResolveComponent): void {
    _resolve = resolve;

    http.onResponse((response) => {
        if (!response.headers['x-inertia-modal'] || !_currentPage) {
            return response;
        }

        const data = typeof response.data === 'string' ? JSON.parse(response.data) : response.data;

        data.component = _currentPage.component;
        data.props = {
            ...JSON.parse(JSON.stringify(_currentPage.props)),
            ...data.props,
        };

        for (const key of preserveKeys) {
            const currentValue = (_currentPage as Record<string, unknown>)[key];

            if (currentValue) {
                data[key] = mergePageData(currentValue, data[key]);
            }
        }

        response.data = data;
        response.headers['x-inertia'] = 'true';

        return response;
    });
}

export function useModal() {
    const page = usePage<{ modal?: ModalData }>();
    const modal = page.props.modal ?? null;

    return {
        modal,
        close: () => router.visit(modal?.redirectURL ?? '/'),
    };
}

/**
 * Drop this inside any persistent layout. It renders nothing when there is
 * no active modal and renders the modal page component otherwise.
 */
export function Modal() {
    const page = usePage<{ modal?: ModalData }>();
    const modal = page.props.modal;

    const [ResolvedComponent, setResolvedComponent] = useState<ModalComponent | null>(null);
    // Track which component name is currently resolved to gate rendering
    const [resolvedForName, setResolvedForName] = useState<string | null>(null);

    // Keep the backdrop reference fresh for the HTTP interceptor
    useEffect(() => {
        _currentPage = { component: page.component, props: page.props };
    });

    // Re-register the router.before listener when the modal key or redirect URL changes
    const modalKey = modal?.key ?? null;
    const modalRedirectURL = modal?.redirectURL ?? null;

    useEffect(() => {
        return router.on('before', (event) => {
            if (modalKey) {
                event.detail.visit.headers['X-Inertia-Modal-Key'] = modalKey;
            }

            if (modalRedirectURL) {
                event.detail.visit.headers['X-Inertia-Modal-Redirect'] = modalRedirectURL;
            }
        });
    }, [modalKey, modalRedirectURL]);

    // Load the modal component when the component name changes
    useEffect(() => {
        if (!modal?.component || !_resolve || modal.component === resolvedForName) {
            return;
        }

        const name = modal.component;

        _resolve(name).then((module) => {
            setResolvedComponent(() => module.default);
            setResolvedForName(name);
        });
    }, [modal?.component, resolvedForName]);

    if (!modal?.component || !ResolvedComponent || resolvedForName !== modal.component) {
        return null;
    }

    return <ResolvedComponent key={modal.key} {...modal.props} />;
}