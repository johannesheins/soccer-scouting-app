import { http, router, usePage } from '@inertiajs/react';
import React, { lazy, Suspense, useEffect, useRef } from 'react';

type ModalData = {
    component: string;
    redirectURL: string;
    props: Record<string, unknown>;
    key: string;
};

type ResolveComponent = (name: string) => Promise<{ default: React.ComponentType<Record<string, unknown>> }>;

// Module-level state shared between the HTTP interceptor and the Modal component
let _resolve: ResolveComponent | null = null;
let _currentPage: { component: string; props: Record<string, unknown> } | null = null;

const preserveKeys = [
    'scrollProps', 'mergeProps', 'prependProps', 'deepMergeProps',
    'matchPropsOn', 'deferredProps', 'sharedProps', 'onceProps',
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
    const modalRef = useRef(modal);
    modalRef.current = modal;

    // Keep the backdrop reference fresh for the HTTP interceptor
    useEffect(() => {
        _currentPage = { component: page.component, props: page.props };
    });

    // Attach router.before listener once; reads modalRef to avoid stale closures
    useEffect(() => {
        return router.on('before', (event) => {
            const m = modalRef.current;
            if (m?.key) {
                event.detail.visit.headers['X-Inertia-Modal-Key'] = m.key;
            }
            if (m?.redirectURL) {
                event.detail.visit.headers['X-Inertia-Modal-Redirect'] = m.redirectURL;
            }
        });
    }, []);

    // Cache lazy-loaded modal components by component name
    const cache = useRef<Record<string, React.LazyExoticComponent<React.ComponentType<Record<string, unknown>>>>>({});

    if (!modal?.component || !_resolve) {
        return null;
    }

    const resolve = _resolve;

    if (!cache.current[modal.component]) {
        cache.current[modal.component] = lazy(() => resolve(modal.component));
    }

    const ModalComponent = cache.current[modal.component];

    return (
        <Suspense>
            <ModalComponent key={modal.key} {...modal.props} />
        </Suspense>
    );
}