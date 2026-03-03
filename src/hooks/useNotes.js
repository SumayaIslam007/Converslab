import { useState, useEffect } from '@wordpress/element';
import { fetchNotes, deleteNote as apiDeleteNote } from '../api/notes';

export const useNotes = () => {
    const [notes, setNotes] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState(null);
    const [refreshCount, setRefreshCount] = useState(0);

    // This triggers the useEffect to run again
    const refreshNotes = () => setRefreshCount((count) => count + 1);

    useEffect(() => {
        setIsLoading(true);
        fetchNotes()
            .then((data) => {
                setNotes(data);
                setIsLoading(false);
            })
            .catch((err) => {
                setError(err.message || 'An error occurred.');
                setIsLoading(false);
            });
    }, [refreshCount]);

    const removeNote = (id) => {
        if (!window.confirm('Are you sure you want to delete this note?')) return;
        
        apiDeleteNote(id)
            .then(() => refreshNotes())
            .catch((err) => alert('Failed to delete: ' + err.message));
    };

    return { notes, isLoading, error, refreshNotes, removeNote };
};