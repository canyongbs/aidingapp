import DOMPurify from 'dompurify';
import { marked } from 'marked';

marked.setOptions({
    breaks: true,
    gfm: true,
});

export function useMarkdown() {
    const renderMarkdown = (content) => {
        if (!content) return '';
        try {
            const html = marked.parse(content, { async: false });
            return DOMPurify.sanitize(html);
        } catch (error) {
            console.error('Error rendering markdown:', error);
            return DOMPurify.sanitize(content);
        }
    };

    return { renderMarkdown };
}
