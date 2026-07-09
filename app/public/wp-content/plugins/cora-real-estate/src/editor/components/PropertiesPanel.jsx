export default function PropertiesPanel({ activeElement, controls, settings, updateSetting }) {
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

    return (
        <div style={{ display: 'flex', flexDirection: 'column', height: '100%' }}>
            <div style={{ padding: '16px 24px', borderBottom: '1px solid #e4e4e7', backgroundColor: '#fafafa' }}>
                <h3 style={{ margin: 0, fontSize: '14px', fontWeight: 600, textTransform: 'capitalize' }}>
                    {activeElement.replace('_', ' ')} Properties
                </h3>
            </div>
            
            <div style={{ padding: '24px', flex: 1, overflowY: 'auto' }}>
                {controls && controls.length > 0 ? (
                    <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                        {controls.map((ctrl, i) => {
                            // Filter out internal controls starting with _
                            if (ctrl.startsWith('_')) return null;
                            
                            return (
                                <li key={i} style={{ marginBottom: '16px' }}>
                                    <label style={{ display: 'block', fontSize: '12px', color: '#71717a', marginBottom: '8px', textTransform: 'capitalize' }}>
                                        {ctrl.replace(/_/g, ' ')}
                                    </label>
                                    {typeof settings[ctrl] === 'string' || typeof settings[ctrl] === 'number' ? (
                                        <input 
                                            type="text" 
                                            value={settings[ctrl] || ''}
                                            onChange={(e) => updateSetting(ctrl, e.target.value)}
                                            placeholder="Value..."
                                            style={{
                                                width: '100%',
                                                padding: '8px 12px',
                                                border: '1px solid #e4e4e7',
                                                borderRadius: '4px',
                                                fontSize: '13px',
                                                backgroundColor: '#ffffff',
                                                color: '#18181b',
                                                outline: 'none'
                                            }} 
                                        />
                                    ) : (
                                        <div style={{ fontSize: '12px', color: '#a1a1aa', fontStyle: 'italic' }}>
                                            [Complex Control Type]
                                        </div>
                                    )}
                                </li>
                            );
                        })}
                    </ul>
                ) : (
                    <div style={{ fontSize: '13px', color: '#a1a1aa' }}>No basic controls detected.</div>
                )}
            </div>
        </div>
    );
}
