export default function Sidebar({ addWidget }) {
    const navItems = [
        { label: 'Dashboard', icon: 'M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm10 0h6v6h-6z' },
        { label: 'Pages', icon: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z' },
        { label: 'Media', icon: 'M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z' },
        { label: 'Theme', icon: 'M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1.01-.23-.26-.38-.61-.38-.99 0-.83.67-1.5 1.5-1.5H16c2.76 0 5-2.24 5-5 0-4.42-4.03-8-9-8zm-5.5 9c-.83 0-1.5-.67-1.5-1.5S5.67 9 6.5 9 8 9.67 8 10.5 7.33 12 6.5 12zm3-4C8.67 8 8 7.33 8 6.5S8.67 5 9.5 5s1.5.67 1.5 1.5S10.33 8 9.5 8zm5 0c-.83 0-1.5-.67-1.5-1.5S13.67 5 14.5 5s1.5.67 1.5 1.5S15.33 8 14.5 8zm3 4c-.83 0-1.5-.67-1.5-1.5S16.67 9 17.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z' }
    ];

    const basicWidgets = [
        { type: 'heading', label: 'Heading' },
        { type: 'text-editor', label: 'Text Block' },
        { type: 'button', label: 'Button' },
        { type: 'image', label: 'Image' }
    ];

    return (
        <div style={{ padding: '24px 0', display: 'flex', flexDirection: 'column', height: '100%' }}>
            <div style={{ padding: '0 24px', marginBottom: '32px' }}>
                <h2 style={{ fontSize: '18px', fontWeight: 700, margin: 0, letterSpacing: '0.5px' }}>CORA STUDIO</h2>
            </div>
            
            <div style={{ padding: '0 24px', marginBottom: '16px' }}>
                <span style={{ fontSize: '10px', fontWeight: 700, color: '#a1a1aa', textTransform: 'uppercase', letterSpacing: '0.05em' }}>
                    Drag & Drop Widgets
                </span>
            </div>
            <div style={{ padding: '0 16px', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '8px', marginBottom: '24px' }}>
                {basicWidgets.map((w, i) => (
                    <div 
                        key={i} 
                        onClick={() => addWidget(w.type)}
                        style={{
                            padding: '12px 8px',
                            border: '1px solid #e4e4e7',
                            borderRadius: '6px',
                            textAlign: 'center',
                            cursor: 'pointer',
                            fontSize: '12px',
                            fontWeight: 500,
                            color: '#18181b',
                            transition: 'border 0.2s, background 0.2s',
                            ':hover': { borderColor: '#18181b', backgroundColor: '#f4f4f5' }
                        }}
                    >
                        {w.label}
                    </div>
                ))}
            </div>

            <div style={{ padding: '0 24px', marginBottom: '12px' }}>
                <span style={{ fontSize: '10px', fontWeight: 700, color: '#a1a1aa', textTransform: 'uppercase', letterSpacing: '0.05em' }}>
                    Navigation
                </span>
            </div>
            <nav style={{ flex: 1 }}>
                <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                    {navItems.map((item, i) => (
                        <li key={i} style={{ 
                            padding: '10px 24px', 
                            display: 'flex', 
                            alignItems: 'center',
                            cursor: 'pointer',
                            color: '#71717a',
                            fontWeight: 500,
                            fontSize: '14px',
                            transition: 'color 0.2s',
                            ':hover': { color: '#18181b' }
                        }}>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style={{ marginRight: '16px' }}>
                                <path d={item.icon} />
                            </svg>
                            {item.label}
                        </li>
                    ))}
                </ul>
            </nav>

            <div style={{ padding: '24px' }}>
                <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                    <li style={{ padding: '10px 0', fontSize: '14px', color: '#71717a', cursor: 'pointer', display: 'flex', alignItems: 'center' }}>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style={{ marginRight: '12px' }}>
                            <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.06-.94l2.03-1.58a.49.49 0 0 0 .12-.61l-1.92-3.32a.488.488 0 0 0-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54a.484.484 0 0 0-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.56-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.06.94l-2.03 1.58a.49.49 0 0 0-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .43-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.49-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" />
                        </svg>
                        Settings
                    </li>
                </ul>
            </div>
        </div>
    );
}
