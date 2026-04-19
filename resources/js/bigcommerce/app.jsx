import React from 'react';
import { createRoot } from 'react-dom/client';

function App({ site }) {
    return (
        <div style={{ fontFamily: 'sans-serif', maxWidth: 640, margin: '40px auto', padding: '0 16px' }}>
            <h1 style={{ fontSize: '1.25rem', marginBottom: 24 }}>
                BigCommerce — {site.name}
            </h1>
            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '0.9rem' }}>
                <tbody>
                    {Object.entries(site).map(([key, value]) => (
                        <tr key={key} style={{ borderBottom: '1px solid #e5e7eb' }}>
                            <th style={{ textAlign: 'left', padding: '8px 12px', color: '#6b7280', width: 180 }}>{key}</th>
                            <td style={{ padding: '8px 12px' }}>{value ?? '—'}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

const el = document.getElementById('app');
createRoot(el).render(<App site={JSON.parse(el.dataset.site)} />);
