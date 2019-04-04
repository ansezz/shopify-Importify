import Http from './http'

export default {
    search(url, page) {
        return Http.post('search', {url, page})
    },
    importer(slug, url) {
        return Http.post('importer', {url, slug})
    }
}
