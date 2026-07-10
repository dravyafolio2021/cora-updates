import { useState, useEffect } from '@wordpress/element';

export default function useElementor() {
    const [activeElement, setActiveElement] = useState(null);
    const [activeModel, setActiveModel] = useState(null);
    const [controls, setControls] = useState({});
    const [settings, setSettings] = useState({});
    const [widgets, setWidgets] = useState({});

    useEffect(() => {
        const checkElementor = setInterval(() => {
            if (window.elementor && window.elementor.selection) {
                clearInterval(checkElementor);
                
                // Load all registered widgets from widgetsCache
                if (window.elementor.widgetsCache) {
                    const allWidgets = {};
                    Object.keys(window.elementor.widgetsCache).forEach(key => {
                        const w = window.elementor.widgetsCache[key];
                        // Ensure it has categories and is not a core structural element
                        if (w.categories && key !== 'section' && key !== 'column' && key !== 'container' && key !== 'inner-section') {
                            allWidgets[key] = {
                                type: key,
                                title: w.title || key,
                                icon: w.icon || 'eicon-editor-list',
                                categories: w.categories
                            };
                        }
                    });
                    setWidgets(allWidgets);
                }

                // Listen for Element selection events
                window.elementor.channels.editor.on('section:activated', () => {
                    const view = window.elementor.getPanelView().getCurrentPageView();
                    if (view && view.model) {
                        const model = view.model;
                        const type = model.get('widgetType') || model.get('elType');
                        setActiveElement(type);
                        setActiveModel(model);
                        
                        // Extract settings
                        const currentSettings = model.get('settings').attributes;
                        setSettings({...currentSettings});

                        // Fetch control definitions for this element
                        const widgetConfig = window.elementor.widgetsCache[type];
                        if (widgetConfig && widgetConfig.controls) {
                            setControls(widgetConfig.controls);
                        } else {
                            setControls({});
                        }
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

    // Widget injection
    const addWidget = (widgetType) => {
        if (window.elementor && window.elementor.selection) {
            const elements = window.elementor.selection.getElements();
            if (elements && elements.length > 0) {
                const parent = elements[0];
                const parentElType = parent.get('elType');
                if (parentElType === 'column' || parentElType === 'container') {
                    window.elementor.channels.data.trigger('element:insert', {
                        elType: 'widget',
                        widgetType: widgetType
                    }, parent);
                } else {
                    if (window.parent && window.parent.coraShowToast) {
                        window.parent.coraShowToast('Please select a column or container to insert a widget into.');
                    }
                }
            } else {
                if (window.parent && window.parent.coraShowToast) {
                    window.parent.coraShowToast('Please select a column or container first.');
                }
            }
        }
    };

    return { activeElement, controls, settings, updateSetting, addWidget, widgets };
}
