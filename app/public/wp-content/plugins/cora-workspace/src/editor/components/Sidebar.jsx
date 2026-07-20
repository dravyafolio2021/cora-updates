import { useState } from '@wordpress/element';

export default function Sidebar({ addWidget, widgets }) {
    const [searchQuery, setSearchQuery] = useState('');

    const navItems = [
        { label: 'Dashboard', icon: 'M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm10 0h6v6h-6z' },
        { label: 'Pages', icon: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z' },
        { label: 'Media', icon: 'M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z' },
        { label: 'Theme', icon: 'M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1.01-.23-.26-.38-.61-.38-.99 0-.83.67-1.5 1.5-1.5H16c2.76 0 5-2.24 5-5 0-4.42-4.03-8-9-8zm-5.5 9c-.83 0-1.5-.67-1.5-1.5S5.67 9 6.5 9 8 9.67 8 10.5 7.33 12 6.5 12zm3-4C8.67 8 8 7.33 8 6.5S8.67 5 9.5 5s1.5.67 1.5 1.5S10.33 8 9.5 8zm5 0c-.83 0-1.5-.67-1.5-1.5S13.67 5 14.5 5s1.5.67 1.5 1.5S15.33 8 14.5 8zm3 4c-.83 0-1.5-.67-1.5-1.5S16.67 9 17.5 9s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z' }
    ];

    // Build the widget list. Fall back to core basic ones if empty/loading
    const hasDynamicWidgets = widgets && Object.keys(widgets).length > 0;
    const widgetList = hasDynamicWidgets 
        ? Object.values(widgets) 
        : [
            { type: 'heading', title: 'Heading', categories: ['basic'] },
            { type: 'text-editor', title: 'Text Block', categories: ['basic'] },
            { type: 'button', title: 'Button', categories: ['basic'] },
            { type: 'image', title: 'Image', categories: ['basic'] },
            { type: 'video', title: 'Video', categories: ['basic'] },
            { type: 'spacer', title: 'Spacer', categories: ['basic'] }
        ];

    // Filter by search query
    const filteredWidgets = widgetList.filter(w => 
        w.title.toLowerCase().includes(searchQuery.toLowerCase()) || 
        w.type.toLowerCase().includes(searchQuery.toLowerCase())
    );

    // Group by category
    const categoriesMap = {};
    filteredWidgets.forEach(w => {
        const cat = (w.categories && w.categories[0]) || 'general';
        if (!categoriesMap[cat]) {
            categoriesMap[cat] = [];
        }
        categoriesMap[cat].push(w);
    });

    return (
        <div style={{ padding: '24px 0', display: 'flex', flexDirection: 'column', height: '100%', overflowY: 'auto' }}>
            <style>{`
                .cora-sidebar-widget {
                    padding: 12px 8px;
                    border: 1px solid #e4e4e7;
                    border-radius: 6px;
                    text-align: center;
                    cursor: pointer;
                    font-size: 11px;
                    font-weight: 500;
                    color: #18181b;
                    text-overflow: ellipsis;
                    overflow: hidden;
                    white-space: nowrap;
                    background-color: #ffffff;
                    transition: all 0.2s ease;
                }
                .cora-sidebar-widget:hover {
                    border-color: #18181b !important;
                    background-color: #fafafa !important;
                    transform: translateY(-1px);
                }
                .cora-sidebar-nav-item {
                    padding: 8px 24px; 
                    display: flex; 
                    align-items: center;
                    cursor: pointer;
                    color: #71717a;
                    font-weight: 500;
                    font-size: 13px;
                    transition: all 0.2s ease;
                }
                .cora-sidebar-nav-item:hover {
                    color: #18181b !important;
                    background-color: #fafafa;
                }
                .cora-search-input {
                    width: 100%;
                    padding: 8px 12px;
                    border: 1px solid #e4e4e7;
                    border-radius: 6px;
                    font-size: 12px;
                    outline: none;
                    color: #18181b;
                    background-color: #ffffff;
                    transition: border-color 0.2s;
                }
                .cora-search-input:focus {
                    border-color: #18181b;
                }
            `}</style>

            <div style={{ padding: '0 24px', marginBottom: '20px' }}>
                <h2 style={{ fontSize: '18px', fontWeight: 700, margin: 0, letterSpacing: '0.5px' }}>CORA STUDIO</h2>
            </div>

            {/* Search Box */}
            <div style={{ padding: '0 24px', marginBottom: '20px' }}>
                <input 
                    type="text" 
                    placeholder="Search widgets..." 
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    className="cora-search-input"
                />
            </div>
            
            {/* Scrollable Widget Catalog */}
            <div style={{ flex: 1, padding: '0 24px', marginBottom: '20px' }}>
                {Object.keys(categoriesMap).length > 0 ? (
                    Object.keys(categoriesMap).map((catName) => (
                        <div key={catName} style={{ marginBottom: '20px' }}>
                            <div style={{ marginBottom: '8px' }}>
                                <span style={{ fontSize: '10px', fontWeight: 700, color: '#a1a1aa', textTransform: 'uppercase', letterSpacing: '0.05em' }}>
                                    {catName} Widgets
                                </span>
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '8px' }}>
                                {categoriesMap[catName].map((w, idx) => (
                                    <div 
                                        key={idx} 
                                        onClick={() => addWidget(w.type)}
                                        className="cora-sidebar-widget"
                                        title={w.title}
                                    >
                                        {w.title}
                                    </div>
                                ))}
                            </div>
                        </div>
                    ))
                ) : (
                    <div style={{ fontSize: '12px', color: '#a1a1aa', textAlign: 'center' }}>No widgets match search.</div>
                )}
            </div>

            {/* Navigation (Sticky to bottom) */}
            <div style={{ borderTop: '1px solid #e4e4e7', paddingTop: '16px' }}>
                <div style={{ padding: '0 24px', marginBottom: '8px' }}>
                    <span style={{ fontSize: '10px', fontWeight: 700, color: '#a1a1aa', textTransform: 'uppercase', letterSpacing: '0.05em' }}>
                        Navigation
                    </span>
                </div>
                <nav>
                    <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                        {navItems.map((item, i) => (
                            <li key={i} className="cora-sidebar-nav-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style={{ marginRight: '12px', color: '#a1a1aa' }}>
                                    <path d={item.icon} />
                                </svg>
                                {item.label}
                            </li>
                        ))}
                    </ul>
                </nav>
            </div>
        </div>
    );
}
