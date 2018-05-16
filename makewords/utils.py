#### helpers

def best_n(a, key, n):
    "return the highest scoring items"
    return sorted(a, key=key, reverse=True)[:n]

def weighted_choice(items):
    "Return most likely the best score but not nessarily."
    weights = map(portman_score, items)
    rnd = random.random() * sum(weights)
    for i, w in enumerate(weights):
        rnd -= w
        if rnd < 0:
            return ''.join(items[i])

def debug_print(arg):
    def wrapper(f):
        def _f(*args, **kwargs):
            best = f(*args)
            print("\n{:25}{:15}{:25}\n{}".format(
                "word", "score", "parts", "="*75))
            for triple in best:
                print("{:25}{:<15.4f}{:25}".format(
                    ''.join(triple), arg(triple), ' '.join(triple)))
            return best
        return _f
    return wrapper