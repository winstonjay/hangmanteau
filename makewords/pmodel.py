from __future__ import division

import math

def load_model(filename):
    data = {}
    with open(filename) as fp:
        for line in fp:
            w, score = line.split()
            data[w] = float(score)
    return Pdist(data)

class Pdist(dict):
    "Get probabilty distrubution using Counter class as input."

    def __init__(self, data=dict(), N=None, missingfn=None):
        "data should be key, int or float value pairs"
        self.N = float(N or sum(data.values()))
        for key, count in data.items():
            self[key] = count
        self.missingfn = missingfn or (lambda k, N: 1./(N * 10**3))

    def __call__(self, key):
        "Return probability dist of the given key."
        return (self[key] / self.N
                if key in self
                else self.missingfn(key, self.N))

    def log10_pword(self, word, n=3):
        tris = n_grams(word, n)
        prod = len(word)
        for p in list(map(self, tris)):
            prod *= math.log10(p)
        return prod

    def log_pword(self, word):
        prod = 1
        for p in word:
            prod *= math.log10(self(p))
        return prod

def n_grams(w, N=3):
    w = w.lower()
    return [w[i:i+N] for i in range(len(w)+1-N)]