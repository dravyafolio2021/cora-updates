import { render } from '@wordpress/element';
import App from './editor/App';

document.addEventListener('DOMContentLoaded', () => {
    // We only mount if we are inside Elementor
    if (document.body.classList.contains('elementor-editor-active')) {
        const root = document.createElement('div');
        root.id = 'cora-elementor-root';
        root.style.position = 'fixed';
        root.style.top = '0';
        root.style.left = '0';
        root.style.width = '100vw';
        root.style.height = '100vh';
        root.style.zIndex = '9999999';
        root.style.display = 'flex';
        root.style.pointerEvents = 'none'; // Allow clicking through to iframe where needed
        document.body.appendChild(root);

        render(<App />, root);
    }
});
