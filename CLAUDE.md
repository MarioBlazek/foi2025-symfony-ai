# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **workshop repository** for teaching Symfony AI integration to students at FOI (Faculty of Organization and Informatics, University of Zagreb). The project is a Symfony 7.3 application that demonstrates different approaches to building constrained AI agents using the Symfony AI Bundle.

The application implements an FOI Assistant chatbot that uses OpenAI's GPT models to answer questions about the university faculty. The primary learning objective is to explore various strategies for constraining LLM responses to stay on-topic.

## Development Commands

### Running the Application

```bash
# Run the basic FOI Assistant chatbot (interactive console)
php bin/console app:foi-assistant

# Run the multi-agent validated FOI Assistant (OpenAI + Anthropic)
php bin/console app:foi-validated-assistant

# Clear cache
php bin/console cache:clear

# Install dependencies
composer install
```

### Environment Setup

The application requires API keys configured in `.env`:
```
OPENAI_API_KEY=your_key_here
ANTHROPIC_API_KEY=your_key_here  # Required for multi-agent validation example
```

## Architecture Overview

### AI Agent System

The project explores multiple approaches to constraining LLM responses using the Symfony AI Bundle:

1. **FOIAssistantAgent** (src/Agent/FOIAssistantAgent.php) - Basic approach using system prompts with FOI context
2. **FOIAssistantAgentV1** (src/Agent/FOIAssistantAgentV1.php) - Enhanced system prompts with strict instructions to reject off-topic questions
3. **FOIAssistantAgentV2** (src/Agent/FOIAssistantAgentV2.php) - Pre-filter approach that uses a separate AI call to check question relevance before answering
4. **FOIAssistantAgentV3** (src/Agent/FOIAssistantAgentV3.php) - Structured classification with confidence scores and JSON response format

These represent different strategies for building constrained AI assistants (see README.md for detailed approach comparisons).

### Multi-Agent Validation Workflow

A more advanced example demonstrating agent orchestration and quality assurance:

**FOIStudentQuestionAgent** (src/Agent/FOIStudentQuestionAgent.php):
- Uses OpenAI GPT-4o-mini to answer student questions about FOI
- Provides detailed information about programs, facilities, admissions, etc.

**AnswerValidatorAgent** (src/Agent/AnswerValidatorAgent.php):
- Uses Anthropic Claude 3.5 Sonnet to validate answers
- Assesses accuracy, completeness, clarity, relevance, and helpfulness
- Provides structured feedback with improvement suggestions
- Demonstrates the `#[Autowire]` attribute for injecting named agent services

**ValidatedFOIAssistantAgent** (src/Agent/ValidatedFOIAssistantAgent.php):
- Orchestrates the two-stage workflow:
  1. Generate answer with OpenAI
  2. Validate answer with Anthropic
- Returns both the answer and validation results
- Demonstrates practical multi-agent collaboration patterns

**ValidatedFOIAssistantCommand** (src/Command/ValidatedFOIAssistantCommand.php):
- Interactive console interface showing both answer and validation
- Demonstrates how different LLM providers can work together
- Useful for teaching quality assurance and agent orchestration concepts

This pattern is valuable for:
- Quality assurance in production AI systems
- Comparing outputs from different LLM providers
- Teaching students about multi-agent architectures
- Demonstrating cross-platform agent collaboration

### Key Components

**Agent Classes** (src/Agent/):
- All agents implement a single `answerQuestion(string $question): string` method
- Agents use `MessageBag` and `Message` classes from Symfony AI to structure conversations
- V2 and V3 use `PlatformInterface` directly for more control over API requests
- V1 uses `AgentInterface` for simplified agent-based interactions

**Console Command** (src/Command/FOIAssistantCommand.php):
- Interactive CLI interface for the FOI Assistant
- Uses Symfony Console components for user interaction
- Currently wired to use `FOIAssistantAgent` (base version)
- To switch implementations, change the constructor dependency injection

**Configuration**:
- `config/packages/ai.yaml` - Configures the Symfony AI Bundle with:
  - OpenAI platform (default agent using gpt-4o-mini)
  - Anthropic platform (validator agent using claude-3-5-sonnet-20241022)
  - Named agent configuration for different use cases
- `config/services.yaml` - Standard Symfony service configuration with autowiring enabled

### Symfony AI Bundle Integration

The project uses three cutting-edge Symfony AI packages (all in dev-main):
- `symfony/ai-agent` - Agent abstraction layer
- `symfony/ai-bundle` - Symfony integration and configuration
- `symfony/ai-platform` - Platform-specific implementations (OpenAI, Anthropic, etc.)

These work together to provide:
- Dependency injection of AI agents and platforms
- Configuration-based model selection
- Message abstraction for different LLM providers
- Support for structured outputs (JSON mode in V3)
- Multi-platform agent orchestration (demonstrated in validation workflow)

## Working with Different Agent Versions

To switch which agent implementation is used:

1. Edit `src/Command/FOIAssistantCommand.php`
2. Change the constructor parameter type from `FOIAssistantAgent` to desired version (e.g., `FOIAssistantAgentV1`, `FOIAssistantAgentV2`, or `FOIAssistantAgentV3`)
3. Update the use statement at the top of the file

Example:
```php
public function __construct(
    private readonly FOIAssistantAgentV2 $agent  // Changed from FOIAssistantAgent
) {
    parent::__construct();
}
```

## Code Standards

- PHP 8.2+ with strict types (`declare(strict_types=1);`)
- PSR-4 autoloading with `App\` namespace mapping to `src/`
- Symfony coding standards and best practices
- Dependency injection via constructor autowiring
