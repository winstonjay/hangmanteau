'''
brainstorming possible features

    feature             notes
    =================================================
    total_len
    start_len
    middle_len
    end_len
    norvig_score        tries to find optional ratio
    noise_score         the lower the better here.
    log10_pword         probablity of the word generated by trigram chunks
    bacon_score         probabilty of composing words (need a dictinary for this (use google)) and amount overlap
    char_probibilty     probabilty of the chars within the text
    vowel_count
    word_similarity     using vector space model compute cosine similarity (needs good model long)


human rating

if we had this then we probally dont need to do anything else we could get a smaller subset
rated then use the features to predict the weights for the features using some basic linear
regression.
'''