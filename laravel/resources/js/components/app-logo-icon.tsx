import type { SVGAttributes } from 'react';
import logo from '@/images/logo.png'

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <img src={logo} id="app-logo" alt="logo"/>
    );
}
