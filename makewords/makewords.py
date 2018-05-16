'''
generate_words.py

Here a portmanteau word is constituted by the combination of two existing words
that have least 2 letters of overlap. For example, `vodkazoo` could be a valid
word as it constructible from the real words  `vodka` and `kazoo`. The
combination is formed as 'vod' + 'ka' + 'zoo'. Words are generated randomly
but are scored to try and produce the most interesting examples.

The bulk of this file comes from Peter Norvig's design of computer progams
course on Udacity. https://udacity.com/course/design-of-computer-programs--cs212

eg use:
    $ python makewords.py -w="data/source.txt" -s=2 > testresults/strategy2

`--help` for more info.
'''
from __future__ import print_function
from __future__ import division

import argparse
import collections

import strategies

def portmanteau_words(words, strategy, n=50):
    '''Given a list of words and a strategy, find the best n list of
    portmanteu words.'''
    candidates = generate_candidates(words)
    if not candidates:
        return []
    return strategy(candidates, n)

cat = ''.join


def generate_candidates(words):
    """All (start, mid, end) pairs where start+mid and mid+end are in words
    (and all three parts are non-empty)."""
    # First compute all {mid: [end]} pairs, then for each (start, mid) pair,
    # grab the [end...] entries, if any.  This approach make two O(N)
    # passes over the words (and O(number of letters) for each word), but is
    # much more efficient than the naive O(N^2) algorithm of looking at all
    # word pairs.
    ends = compute_ends(words)
    return ((start, mid, end)
            for word in words
            for start, mid in splits(word)
            for end in ends[mid]
            if word != mid+end and len(mid) > 2)

def splits(word):
    "Return a list of splits of the word w into two non-empty pieces."
    return [(word[:i], word[i:]) for i in range(2, len(w))]

def compute_ends(words):
    "Return a dict of {mid: [end, ...]} entries."
    ends = collections.defaultdict(list)
    for word in words:
        for mid, end in splits(word):
            ends[mid].append(end)
    return ends


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument(
        "-w",
        "--wordsfile",
        help="Source of words (text file 1 word per line).",
        type=str,
        required=True)
    parser.add_argument(
        "-s",
        "--strategy",
        help="Strategy number from `strategies.py`. 1-4",
        type=int,
        required=True)
    parser.add_argument(
        "-o",
        "--out",
        help="filename to write results to.")
    parser.add_argument(
        "-k",
        "--sample_count",
        help="Number of samples to generate",
        type=int,
        default=100)
    args = parser.parse_args()


    with open(args.wordsfile) as fp:
        words = [w for w in fp.read().split() if len(w) > 2]

    strategy_name = "strategy%d" % args.strategy
    strategy = getattr(strategies, strategy_name)

    if strategy is None:
        print("ERROR: strategy '%s' does not exist" % strategy_name)
    else:
        parts = portmanteau_words(words, strategy, n=args.sample_count)
        words = map(cat, parts)
        if args.out:
            with open(args.out, "w") as fp:
                for word, tripple in zip(words, parts):
                    fp.write("%s\t%s\n" % (word, " ".join(tripple)))

