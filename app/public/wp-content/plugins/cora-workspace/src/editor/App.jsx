import { useState } from '@wordpress/element';
import Sidebar from './components/Sidebar';
import PropertiesPanel from './components/PropertiesPanel';
import useElementor from './hooks/useElementor';

export default function App() {
    const { activeElement, controls, settings, updateSetting, addWidget, widgets } = useElementor();

    return (
        <div style={{
            display: 'flex',
            width: '100%',
            height: '100%',
            pointerEvents: 'none'
        }}>
            {/* Left Sidebar */}
            <div style={{
                width: '260px',
                height: '100%',
                backgroundColor: '#ffffff',
                borderRight: '1px solid #e4e4e7',
                pointerEvents: 'auto',
                display: 'flex',
                flexDirection: 'column'
            }}>
                <Sidebar addWidget={addWidget} widgets={widgets} />
            </div>

            {/* Main Canvas Area (Transparent to show Elementor Iframe) */}
            <div style={{
                flex: 1,
                position: 'relative',
                pointerEvents: 'none' // Let clicks pass through to iframe
            }}>
                {/* Topbar */}
                <div style={{
                    position: 'absolute',
                    top: 0,
                    left: 0,
                    right: 0,
                    height: '56px',
                    backgroundColor: '#ffffff',
                    borderBottom: '1px solid #e4e4e7',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'flex-end',
                    padding: '0 24px',
                    pointerEvents: 'auto'
                }}>
                    <button style={{
                        padding: '6px 16px',
                        backgroundColor: '#ffffff',
                        border: '1px solid #e4e4e7',
                        borderRadius: '6px',
                        marginRight: '12px',
                        color: '#18181b',
                        fontWeight: 500,
                        cursor: 'pointer'
                    }} onClick={() => {
                        if (window.elementor && window.elementor.saver) {
                            window.elementor.saver.saveAutoSave();
                        }
                    }}>Save Draft</button>
                    <button style={{
                        padding: '6px 16px',
                        backgroundColor: '#18181b',
                        border: 'none',
                        borderRadius: '6px',
                        color: '#ffffff',
                        fontWeight: 500,
                        cursor: 'pointer'
                    }} onClick={() => {
                        if (window.elementor && window.elementor.saver) {
                            window.elementor.saver.doSave();
                        }
                    }}>Publish</button>
                </div>
            </div>

            {/* Right Properties Panel */}
            <div style={{
                width: '300px',
                height: '100%',
                backgroundColor: '#ffffff',
                borderLeft: '1px solid #e4e4e7',
                pointerEvents: 'auto',
                display: 'flex',
                flexDirection: 'column'
            }}>
                <PropertiesPanel activeElement={activeElement} controls={controls} settings={settings} updateSetting={updateSetting} />
            </div>
        </div>
    );
}
