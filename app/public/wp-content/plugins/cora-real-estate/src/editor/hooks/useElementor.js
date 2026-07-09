import { useState, useEffect } from '@wordpress/element';

export default function useElementor() {
    const [activeElement, setActiveElement] = useState(null);
    const [activeModel, setActiveModel] = useState(null);
    const [controls, setControls] = useState([]);
    const [settings, setSettings] = useState({});

    useEffect(() => {
        const checkElementor = setInterval(() => {
            if (window.elementor && window.elementor.selection) {
                clearInterval(checkElementor);
                
                // Listen for Element selection events
                window.elementor.channels.editor.on('section:activated', () => {
                    const view = window.elementor.getPanelView().getCurrentPageView();
                    if (view && view.model) {
                        const model = view.model;
                        setActiveElement(model.get('widgetType') || model.get('elType'));
                        setActiveModel(model);
                        
                        // Extract settings
                        const currentSettings = model.get('settings').attributes;
                        setSettings({...currentSettings});
                        setControls(Object.keys(currentSettings).filter(k => typeof currentSettings[k] !== 'object'));
                    }
                });

                // Listen for setting changes from within Elementor canvas (e.g. dragging)
                window.elementor.channels.editor.on('change', (model) => {
                    if (activeModel && model.id === activeModel.id) {
                        setSettings({...model.get('settings').attributes});
                    }
                });
            }
        }, 1000);

        return () => clearInterval(checkElementor);
    }, [activeModel]);

    // Update setting back into Elementor's Backbone model
    const updateSetting = (key, value) => {
        if (activeModel) {
            // Update local React state instantly for snappy UI
            setSettings(prev => ({...prev, [key]: value}));
            
            // Push to Elementor Backbone Model
            activeModel.setSetting(key, value);
            
            // Re-render the view
            const view = window.elementor.getPanelView().getCurrentPageView();
            if (view && view.render) {
                // Some widgets require full re-render, others bind automatically
                activeModel.trigger('change:settings');
            }
        }
    };

    // Very naive widget injection
    const addWidget = (widgetType) => {
        if (window.elementor && window.elementor.selection) {
            const elements = window.elementor.selection.getElements();
            if (elements && elements.length > 0) {
                const parent = elements[0];
                // Only columns can accept widgets usually, but for PoC we just try:
                if (parent.get('elType') === 'column') {
                    window.elementor.channels.data.trigger('element:insert', {
                        elType: 'widget',
                        widgetType: widgetType
                    }, parent);
                } else {
                    alert('Please select a column to insert a widget into.');
                }
            } else {
                alert('Please select a section/column first.');
            }
        }
    };

    return { activeElement, controls, settings, updateSetting, addWidget };
}
