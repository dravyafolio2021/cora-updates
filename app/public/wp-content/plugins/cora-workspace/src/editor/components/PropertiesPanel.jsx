import { useState } from '@wordpress/element';

export default function PropertiesPanel({ activeElement, controls, settings, updateSetting }) {
    const [activeTab, setActiveTab] = useState('content');

    if (!activeElement) {
        return (
            <div style={{ padding: '24px', color: '#71717a', fontSize: '13px', textAlign: 'center', marginTop: '20px' }}>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" style={{ margin: '0 auto 12px', display: 'block', opacity: 0.5 }}>
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/>
                </svg>
                Select an element on the canvas to view its properties.
            </div>
        );
    }

    // Map control definitions to categories/tabs
    const contentControls = [];
    const styleControls = [];
    const advancedControls = [];

    Object.keys(controls).forEach(key => {
        // Skip internal underscore keys
        if (key.startsWith('_')) return;

        const ctrl = controls[key];
        const tab = ctrl.tab || 'content'; // Default to content tab

        const controlObj = {
            key,
            ...ctrl
        };

        if (tab === 'style') {
            styleControls.push(controlObj);
        } else if (tab === 'advanced') {
            advancedControls.push(controlObj);
        } else {
            contentControls.push(controlObj);
        }
    });

    // Determine controls for active tab
    let activeTabControls = contentControls;
    if (activeTab === 'style') {
        activeTabControls = styleControls;
    } else if (activeTab === 'advanced') {
        activeTabControls = advancedControls;
    }

    const renderControlField = (ctrl) => {
        const value = settings[ctrl.key] !== undefined ? settings[ctrl.key] : (ctrl.default || '');

        switch (ctrl.type) {
            case 'select':
                return (
                    <select 
                        value={value}
                        onChange={(e) => updateSetting(ctrl.key, e.target.value)}
                        className="cora-prop-select"
                    >
                        {ctrl.options && Object.keys(ctrl.options).map(optKey => (
                            <option key={optKey} value={optKey}>
                                {ctrl.options[optKey]}
                            </option>
                        ))}
                    </select>
                );
            case 'switcher':
                const isChecked = value === 'yes' || value === true || value === '1' || value === 1;
                return (
                    <label className="cora-switcher-label">
                        <input 
                            type="checkbox"
                            checked={isChecked}
                            onChange={(e) => updateSetting(ctrl.key, e.target.checked ? 'yes' : '')}
                            className="cora-switcher-input"
                        />
                        <span className="cora-switcher-slider"></span>
                    </label>
                );
            case 'textarea':
            case 'wysiwyg':
            case 'code':
                return (
                    <textarea 
                        value={value}
                        onChange={(e) => updateSetting(ctrl.key, e.target.value)}
                        placeholder="Enter text..."
                        className="cora-prop-textarea"
                        rows={3}
                    />
                );
            case 'color':
                return (
                    <div style={{ display: 'flex', gap: '8px', alignItems: 'center' }}>
                        <input 
                            type="color" 
                            value={typeof value === 'string' && value.startsWith('#') ? value : '#000000'}
                            onChange={(e) => updateSetting(ctrl.key, e.target.value)}
                            style={{
                                width: '32px',
                                height: '32px',
                                padding: 0,
                                border: '1px solid #e4e4e7',
                                borderRadius: '4px',
                                cursor: 'pointer',
                                backgroundColor: 'transparent'
                            }}
                        />
                        <input 
                            type="text" 
                            value={value}
                            onChange={(e) => updateSetting(ctrl.key, e.target.value)}
                            placeholder="#000000"
                            className="cora-prop-input"
                            style={{ flex: 1 }}
                        />
                    </div>
                );
            default:
                // Fallback to text input for any other type (text, number, url, slider, etc.)
                const displayVal = typeof value === 'object' ? JSON.stringify(value) : value;
                return (
                    <input 
                        type="text" 
                        value={displayVal}
                        onChange={(e) => updateSetting(ctrl.key, e.target.value)}
                        placeholder="Value..."
                        className="cora-prop-input"
                    />
                );
        }
    };

    return (
        <div style={{ display: 'flex', flexDirection: 'column', height: '100%' }}>
            <style>{`
                .cora-prop-input, .cora-prop-select, .cora-prop-textarea {
                    width: 100%;
                    padding: 8px 12px;
                    border: 1px solid #e4e4e7;
                    border-radius: 6px;
                    font-size: 12px;
                    color: #18181b;
                    background-color: #ffffff;
                    outline: none;
                    transition: border-color 0.2s;
                }
                .cora-prop-input:focus, .cora-prop-select:focus, .cora-prop-textarea:focus {
                    border-color: #18181b;
                }
                .cora-prop-textarea {
                    resize: vertical;
                }
                
                /* Switcher Slider Style */
                .cora-switcher-label {
                    position: relative;
                    display: inline-block;
                    width: 36px;
                    height: 20px;
                }
                .cora-switcher-input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }
                .cora-switcher-slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #e4e4e7;
                    transition: .3s;
                    border-radius: 20px;
                }
                .cora-switcher-slider:before {
                    position: absolute;
                    content: "";
                    height: 14px;
                    width: 14px;
                    left: 3px;
                    bottom: 3px;
                    background-color: white;
                    transition: .3s;
                    border-radius: 50%;
                }
                .cora-switcher-input:checked + .cora-switcher-slider {
                    background-color: #18181b;
                }
                .cora-switcher-input:checked + .cora-switcher-slider:before {
                    transform: translateX(16px);
                }

                /* Tab styling */
                .cora-prop-tab-btn {
                    padding: 8px 12px;
                    background: none;
                    border: none;
                    font-size: 11px;
                    font-weight: 600;
                    color: #71717a;
                    cursor: pointer;
                    position: relative;
                    transition: color 0.2s;
                }
                .cora-prop-tab-btn.active {
                    color: #18181b;
                }
                .cora-prop-tab-btn.active::after {
                    content: '';
                    position: absolute;
                    bottom: -1px;
                    left: 12px;
                    right: 12px;
                    height: 2px;
                    background-color: #18181b;
                }
            `}</style>

            <div style={{ padding: '16px 24px', borderBottom: '1px solid #e4e4e7', backgroundColor: '#fafafa' }}>
                <h3 style={{ margin: 0, fontSize: '13px', fontWeight: 600, textTransform: 'capitalize' }}>
                    {activeElement.replace('_', ' ')} Properties
                </h3>
            </div>

            {/* Properties Tabs */}
            <div style={{ display: 'flex', borderBottom: '1px solid #e4e4e7', padding: '0 12px', backgroundColor: '#fafafa' }}>
                <button 
                    onClick={() => setActiveTab('content')} 
                    className={`cora-prop-tab-btn ${activeTab === 'content' ? 'active' : ''}`}
                >
                    Content
                </button>
                <button 
                    onClick={() => setActiveTab('style')} 
                    className={`cora-prop-tab-btn ${activeTab === 'style' ? 'active' : ''}`}
                >
                    Style
                </button>
                <button 
                    onClick={() => setActiveTab('advanced')} 
                    className={`cora-prop-tab-btn ${activeTab === 'advanced' ? 'active' : ''}`}
                >
                    Advanced
                </button>
            </div>
            
            {/* Scrollable controls list */}
            <div style={{ padding: '20px 24px', flex: 1, overflowY: 'auto' }}>
                {activeTabControls.length > 0 ? (
                    <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                        {activeTabControls.map((ctrl, i) => (
                            <li key={i} style={{ marginBottom: '16px' }}>
                                <label style={{ display: 'block', fontSize: '11px', fontWeight: 600, color: '#71717a', marginBottom: '8px', textTransform: 'capitalize' }}>
                                    {ctrl.label || ctrl.key.replace(/_/g, ' ')}
                                </label>
                                {renderControlField(ctrl)}
                            </li>
                        ))}
                    </ul>
                ) : (
                    <div style={{ fontSize: '12px', color: '#a1a1aa', textAlign: 'center', marginTop: '20px', fontStyle: 'italic' }}>
                        No controls in this section.
                    </div>
                )}
            </div>
        </div>
    );
}
