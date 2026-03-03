import apiFetch from '@wordpress/api-fetch';

const getBaseUrl = () => converselabSettings.restUrl;

export const fetchNotes = () => {
    return apiFetch({ url: getBaseUrl() });
};

export const createNote = (data) => {
    return apiFetch({
        url: getBaseUrl(),
        method: 'POST',
        data,
    });
};

export const updateNote = (id, data) => {
    return apiFetch({
        url: `${getBaseUrl()}/${id}`,
        method: 'PUT',
        data,
    });
};

export const deleteNote = (id) => {
    return apiFetch({
        url: `${getBaseUrl()}/${id}`,
        method: 'DELETE',
    });
};