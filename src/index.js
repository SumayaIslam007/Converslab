import {createRoot, useState, useEffect} from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { createNote, updateNote } from './api/notes';
import {useNotes} from './hooks/useNotes';

apiFetch.use(apiFetch.createNonceMiddleware(converselabSettings.nonce));

const NoteForm = ({ onNoteSaved, editingNote, onCancelEdit }) => {
    const defaultState = { title: '', content: '', priority: 'medium', source_url: '' };
    const [formData, setFormData] = useState(defaultState);
    const [errors, setErrors] = useState({});
    const [isSubmitting, setIsSubmitting] = useState(false);

    useEffect(() => {
        if (editingNote) {
            setFormData({
                title: editingNote.title,
                content: editingNote.content || '',
                priority: editingNote.priority || 'medium',
                source_url: editingNote.source_url || ''
            });
        } else {
            setFormData(defaultState);
        }
    }, [editingNote]);

    const handleSubmit = (e) => {
        e.preventDefault();
        setErrors({});

        let validationErrors = {};
        if (!formData.title.trim()) validationErrors.title = 'Title is required.';
        if (!formData.content.trim()) validationErrors.content = 'Content is required.';

        if (Object.keys(validationErrors).length > 0) {
            setErrors(validationErrors);
            return;
        }

        setIsSubmitting(true);

        const apiCall = editingNote ? updateNote(editingNote.id, formData) : createNote(formData);

        apiCall
            .then(() => {
                setFormData(defaultState);
                setIsSubmitting(false);
                onNoteSaved();
                if (editingNote) onCancelEdit();
            })
            .catch((err) => {
                setErrors({ server: err.message || 'Server rejected the note.' });
                setIsSubmitting(false);
            });
    };

    return (
        <div className="card" style={{ 
            maxWidth: '800px', marginBottom: '20px', padding: '20px', marginTop: '20px', 
            borderLeft: editingNote ? '4px solid #2271b1' : 'none'
        }}>
            <h2 className="title" style={{ marginTop: 0 }}>
                {editingNote ? `Edit Note #${editingNote.id}` : 'Create New Note'}
            </h2>
            
            {errors.server && <div className="notice notice-error inline" style={{marginLeft: 0}}><p>{errors.server}</p></div>}
            
            <form onSubmit={handleSubmit}>
                <div style={{ marginBottom: '15px' }}>
                    <label><strong>Title <span style={{color: '#d63638'}}>*</span></strong></label><br/>
                    <input type="text" className="regular-text" value={formData.title} onChange={e => setFormData({...formData, title: e.target.value})} />
                    {errors.title && <p style={{color: '#d63638', margin: '5px 0 0 0'}}>{errors.title}</p>}
                </div>

                <div style={{ marginBottom: '15px' }}>
                    <label><strong>Content <span style={{color: '#d63638'}}>*</span></strong></label><br/>
                    <textarea className="large-text" rows="4" value={formData.content} onChange={e => setFormData({...formData, content: e.target.value})}></textarea>
                    {errors.content && <p style={{color: '#d63638', margin: '5px 0 0 0'}}>{errors.content}</p>}
                </div>

                <div style={{ marginBottom: '15px' }}>
                    <label><strong>Priority</strong></label><br/>
                    <select value={formData.priority} onChange={e => setFormData({...formData, priority: e.target.value})}>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div style={{ marginBottom: '20px' }}>
                    <label><strong>Source URL</strong></label><br/>
                    <input type="url" className="regular-text" placeholder="https://" value={formData.source_url} onChange={e => setFormData({...formData, source_url: e.target.value})} />
                </div>

                <div style={{ display: 'flex', gap: '10px' }}>
                    <button type="submit" className="button button-primary" disabled={isSubmitting}>
                        {isSubmitting ? 'Saving...' : (editingNote ? 'Update Note' : 'Save Note')}
                    </button>
                    
                    {/* Only show the Cancel button if we are in Edit Mode */}
                    {editingNote && (
                        <button type="button" className="button button-secondary" onClick={onCancelEdit}>
                            Cancel Edit
                        </button>
                    )}
                </div>
            </form>
        </div>
    );
};

const NotesList = ({ notes, isLoading, error, onEditClick, onNoteDeleted }) => {
    if (isLoading) return <p>Loading your Converselab notes...</p>;
    if (error) return <div className="notice notice-error inline"><p><strong>Error:</strong> {error}</p></div>;
    if (notes.length === 0) return <div className="notice notice-info inline"><p>No notes found in the database. Time to create your first one!</p></div>;
    
    return (
        <table className="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th style={{ width: '50px' }}>ID</th>
                    <th>Title</th>
                    <th>Priority</th>
                    <th>Date Created</th>
                    {/* NEW: The Actions Column Header */}
                    <th style={{ width: '150px' }}>Actions</th>
                </tr>
            </thead>
            <tbody>
                {notes.map(note => (
                    <tr key={note.id}>
                        <td>{note.id}</td>
                        <td><strong>{note.title}</strong></td>
                        <td>
                            <span className={`converselab-badge priority-${note.priority}`}>
                                {note.priority ? note.priority.toUpperCase() : 'NORMAL'}
                            </span>
                        </td>
                        <td>{note.date}</td>
                        {/* NEW: The Edit and Delete Buttons */}
                        <td>
                            <button className="button button-small" onClick={() => onEditClick(note)} style={{ marginRight: '8px' }}>
                                Edit
                            </button>
                            <button className="button button-small button-link-delete" onClick={() => handleDelete(note.id)} style={{ color: '#d63638' }}>
                                Delete
                            </button>
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
    );
};

const App = () => {
    // Uses our new custom hook!
    const { notes, isLoading, error, refreshNotes, removeNote } = useNotes();
    const [editingNote, setEditingNote] = useState(null);

    return (
        <div className="wrap">
            <h1 className="wp-heading-inline">Converselab Notes Database</h1>
            <hr className="wp-header-end" />
            
            <NoteForm 
                onNoteSaved={refreshNotes} 
                editingNote={editingNote} 
                onCancelEdit={() => setEditingNote(null)} 
            />
            
            <NotesList
                notes={notes} 
                isLoading={isLoading}
                error={error}
                onNoteDeleted={removeNote}
                onEditClick={(note) => {
                    setEditingNote(note);
                    window.scrollTo(0, 0); // Smoothly scrolls back up to the form!
                }}
            />
        </div>
    );
};

const rootElement=document.getElementById('converselab-admin-app');

if(rootElement){
    const root=createRoot(rootElement);
    root.render(<App />);
}