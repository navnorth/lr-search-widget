<h5>GET /api/search/stats</h5>

<p>Endpoint to return index (ElasticSearch) stats</p>


<strong>Request:</strong>
<pre>{{ URL::to('/api/search/stats') }}?api_key=&lt;API_KEY&gt;</pre>
<strong>Response:</strong>
<pre class="pre-scrollable">
{
    "_all": {
        "primaries": {
            "completion": {
                "size_in_bytes": 0
            },
            "docs": {
                "count": 175174,
                "deleted": 774
            },
            "fielddata": {
                "evictions": 0,
                "memory_size_in_bytes": 0
            },
            "filter_cache": {
                "evictions": 0,
                "memory_size_in_bytes": 47112
            },
            "flush": {
                "total": 0,
                "total_time_in_millis": 0
            },
            "get": {
                "current": 0,
                "exists_time_in_millis": 0,
                "exists_total": 0,
                "missing_time_in_millis": 0,
                "missing_total": 0,
                "time_in_millis": 0,
                "total": 0
            },
            "id_cache": {
                "memory_size_in_bytes": 0
            },
            "indexing": {
                "delete_current": 0,
                "delete_time_in_millis": 0,
                "delete_total": 0,
                "index_current": 0,
                "index_time_in_millis": 0,
                "index_total": 0,
                "is_throttled": false,
                "noop_update_total": 0,
                "throttle_time_in_millis": 0
            },
            "merges": {
                "current": 0,
                "current_docs": 0,
                "current_size_in_bytes": 0,
                "total": 0,
                "total_docs": 0,
                "total_size_in_bytes": 0,
                "total_time_in_millis": 0
            },
            "percolate": {
                "current": 0,
                "memory_size": "-1b",
                "memory_size_in_bytes": -1,
                "queries": 0,
                "time_in_millis": 0,
                "total": 0
            },
            "query_cache": {
                "evictions": 0,
                "hit_count": 0,
                "memory_size_in_bytes": 0,
                "miss_count": 0
            },
            "refresh": {
                "total": 5,
                "total_time_in_millis": 0
            },
            "search": {
                "fetch_current": 0,
                "fetch_time_in_millis": 58,
                "fetch_total": 10,
                "open_contexts": 0,
                "query_current": 0,
                "query_time_in_millis": 1519,
                "query_total": 20
            },
            "segments": {
                "count": 47,
                "fixed_bit_set_memory_in_bytes": 0,
                "index_writer_max_memory_in_bytes": 2560000,
                "index_writer_memory_in_bytes": 0,
                "memory_in_bytes": 2700712,
                "version_map_memory_in_bytes": 0
            },
            "store": {
                "size_in_bytes": 179690781,
                "throttle_time_in_millis": 0
            },
            "suggest": {
                "current": 0,
                "time_in_millis": 0,
                "total": 0
            },
            "translog": {
                "operations": 0,
                "size_in_bytes": 17
            },
            "warmer": {
                "current": 0,
                "total": 10,
                "total_time_in_millis": 22
            }
        },
        "total": {
            "completion": {
                "size_in_bytes": 0
            },
            "docs": {
                "count": 175174,
                "deleted": 774
            },
            "fielddata": {
                "evictions": 0,
                "memory_size_in_bytes": 0
            },
            "filter_cache": {
                "evictions": 0,
                "memory_size_in_bytes": 47112
            },
            "flush": {
                "total": 0,
                "total_time_in_millis": 0
            },
            "get": {
                "current": 0,
                "exists_time_in_millis": 0,
                "exists_total": 0,
                "missing_time_in_millis": 0,
                "missing_total": 0,
                "time_in_millis": 0,
                "total": 0
            },
            "id_cache": {
                "memory_size_in_bytes": 0
            },
            "indexing": {
                "delete_current": 0,
                "delete_time_in_millis": 0,
                "delete_total": 0,
                "index_current": 0,
                "index_time_in_millis": 0,
                "index_total": 0,
                "is_throttled": false,
                "noop_update_total": 0,
                "throttle_time_in_millis": 0
            },
            "merges": {
                "current": 0,
                "current_docs": 0,
                "current_size_in_bytes": 0,
                "total": 0,
                "total_docs": 0,
                "total_size_in_bytes": 0,
                "total_time_in_millis": 0
            },
            "percolate": {
                "current": 0,
                "memory_size": "-1b",
                "memory_size_in_bytes": -1,
                "queries": 0,
                "time_in_millis": 0,
                "total": 0
            },
            "query_cache": {
                "evictions": 0,
                "hit_count": 0,
                "memory_size_in_bytes": 0,
                "miss_count": 0
            },
            "refresh": {
                "total": 5,
                "total_time_in_millis": 0
            },
            "search": {
                "fetch_current": 0,
                "fetch_time_in_millis": 58,
                "fetch_total": 10,
                "open_contexts": 0,
                "query_current": 0,
                "query_time_in_millis": 1519,
                "query_total": 20
            },
            "segments": {
                "count": 47,
                "fixed_bit_set_memory_in_bytes": 0,
                "index_writer_max_memory_in_bytes": 2560000,
                "index_writer_memory_in_bytes": 0,
                "memory_in_bytes": 2700712,
                "version_map_memory_in_bytes": 0
            },
            "store": {
                "size_in_bytes": 179690781,
                "throttle_time_in_millis": 0
            },
            "suggest": {
                "current": 0,
                "time_in_millis": 0,
                "total": 0
            },
            "translog": {
                "operations": 0,
                "size_in_bytes": 17
            },
            "warmer": {
                "current": 0,
                "total": 10,
                "total_time_in_millis": 22
            }
        }
    },
    "_shards": {
        "failed": 0,
        "successful": 5,
        "total": 10
    },
    "indices": {
        "lr-v2": {
            "primaries": {
                "completion": {
                    "size_in_bytes": 0
                },
                "docs": {
                    "count": 175174,
                    "deleted": 774
                },
                "fielddata": {
                    "evictions": 0,
                    "memory_size_in_bytes": 0
                },
                "filter_cache": {
                    "evictions": 0,
                    "memory_size_in_bytes": 47112
                },
                "flush": {
                    "total": 0,
                    "total_time_in_millis": 0
                },
                "get": {
                    "current": 0,
                    "exists_time_in_millis": 0,
                    "exists_total": 0,
                    "missing_time_in_millis": 0,
                    "missing_total": 0,
                    "time_in_millis": 0,
                    "total": 0
                },
                "id_cache": {
                    "memory_size_in_bytes": 0
                },
                "indexing": {
                    "delete_current": 0,
                    "delete_time_in_millis": 0,
                    "delete_total": 0,
                    "index_current": 0,
                    "index_time_in_millis": 0,
                    "index_total": 0,
                    "is_throttled": false,
                    "noop_update_total": 0,
                    "throttle_time_in_millis": 0
                },
                "merges": {
                    "current": 0,
                    "current_docs": 0,
                    "current_size_in_bytes": 0,
                    "total": 0,
                    "total_docs": 0,
                    "total_size_in_bytes": 0,
                    "total_time_in_millis": 0
                },
                "percolate": {
                    "current": 0,
                    "memory_size": "-1b",
                    "memory_size_in_bytes": -1,
                    "queries": 0,
                    "time_in_millis": 0,
                    "total": 0
                },
                "query_cache": {
                    "evictions": 0,
                    "hit_count": 0,
                    "memory_size_in_bytes": 0,
                    "miss_count": 0
                },
                "refresh": {
                    "total": 5,
                    "total_time_in_millis": 0
                },
                "search": {
                    "fetch_current": 0,
                    "fetch_time_in_millis": 58,
                    "fetch_total": 10,
                    "open_contexts": 0,
                    "query_current": 0,
                    "query_time_in_millis": 1519,
                    "query_total": 20
                },
                "segments": {
                    "count": 47,
                    "fixed_bit_set_memory_in_bytes": 0,
                    "index_writer_max_memory_in_bytes": 2560000,
                    "index_writer_memory_in_bytes": 0,
                    "memory_in_bytes": 2700712,
                    "version_map_memory_in_bytes": 0
                },
                "store": {
                    "size_in_bytes": 179690781,
                    "throttle_time_in_millis": 0
                },
                "suggest": {
                    "current": 0,
                    "time_in_millis": 0,
                    "total": 0
                },
                "translog": {
                    "operations": 0,
                    "size_in_bytes": 17
                },
                "warmer": {
                    "current": 0,
                    "total": 10,
                    "total_time_in_millis": 22
                }
            },
            "total": {
                "completion": {
                    "size_in_bytes": 0
                },
                "docs": {
                    "count": 175174,
                    "deleted": 774
                },
                "fielddata": {
                    "evictions": 0,
                    "memory_size_in_bytes": 0
                },
                "filter_cache": {
                    "evictions": 0,
                    "memory_size_in_bytes": 47112
                },
                "flush": {
                    "total": 0,
                    "total_time_in_millis": 0
                },
                "get": {
                    "current": 0,
                    "exists_time_in_millis": 0,
                    "exists_total": 0,
                    "missing_time_in_millis": 0,
                    "missing_total": 0,
                    "time_in_millis": 0,
                    "total": 0
                },
                "id_cache": {
                    "memory_size_in_bytes": 0
                },
                "indexing": {
                    "delete_current": 0,
                    "delete_time_in_millis": 0,
                    "delete_total": 0,
                    "index_current": 0,
                    "index_time_in_millis": 0,
                    "index_total": 0,
                    "is_throttled": false,
                    "noop_update_total": 0,
                    "throttle_time_in_millis": 0
                },
                "merges": {
                    "current": 0,
                    "current_docs": 0,
                    "current_size_in_bytes": 0,
                    "total": 0,
                    "total_docs": 0,
                    "total_size_in_bytes": 0,
                    "total_time_in_millis": 0
                },
                "percolate": {
                    "current": 0,
                    "memory_size": "-1b",
                    "memory_size_in_bytes": -1,
                    "queries": 0,
                    "time_in_millis": 0,
                    "total": 0
                },
                "query_cache": {
                    "evictions": 0,
                    "hit_count": 0,
                    "memory_size_in_bytes": 0,
                    "miss_count": 0
                },
                "refresh": {
                    "total": 5,
                    "total_time_in_millis": 0
                },
                "search": {
                    "fetch_current": 0,
                    "fetch_time_in_millis": 58,
                    "fetch_total": 10,
                    "open_contexts": 0,
                    "query_current": 0,
                    "query_time_in_millis": 1519,
                    "query_total": 20
                },
                "segments": {
                    "count": 47,
                    "fixed_bit_set_memory_in_bytes": 0,
                    "index_writer_max_memory_in_bytes": 2560000,
                    "index_writer_memory_in_bytes": 0,
                    "memory_in_bytes": 2700712,
                    "version_map_memory_in_bytes": 0
                },
                "store": {
                    "size_in_bytes": 179690781,
                    "throttle_time_in_millis": 0
                },
                "suggest": {
                    "current": 0,
                    "time_in_millis": 0,
                    "total": 0
                },
                "translog": {
                    "operations": 0,
                    "size_in_bytes": 17
                },
                "warmer": {
                    "current": 0,
                    "total": 10,
                    "total_time_in_millis": 22
                }
            }
        }
    }
}
</pre>


