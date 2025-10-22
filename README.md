* APPROACH 1: System Prompt with Strong Instructions
* This is the simplest approach - rely on the LLM to follow instructions

Approach 1: System Prompt Instructions (Simplest)

Relies entirely on the LLM's instruction-following capabilities
Strong, clear instructions in the system prompt
Pros: Simple to implement, no extra API calls
Cons: Can be bypassed with clever prompting


Approach 2: Pre-filter with Relevance Check (Recommended for Workshop)

Makes a separate API call to check relevance first
Only proceeds with answering if the question is FOI-related
Pros: More reliable, clear separation of concerns
Cons: Extra API call (doubles cost/latency)

Approach 3: Structured Classification with Confidence

Returns structured JSON with confidence scores
Can handle uncertain cases differently
Pros: Provides transparency, can handle edge cases
Cons: More complex, requires JSON parsing
