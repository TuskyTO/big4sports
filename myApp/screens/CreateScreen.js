import React, { useState } from 'react';
import { View, Text, TextInput, Button } from 'react-native';
import axios from 'axios';

export default function CreateScreen({ navigation, route }) {
  const { loggedInUser } = route.params || {};
  const [question, setQuestion] = useState('');
  const [answer, setAnswer] = useState('');
  const [difficulty, setDifficulty] = useState('1');
  const username = loggedInUser;

  const handleCreate = () => {
    axios.post('http://10.0.2.2/big4sports/backend/api_trivia.php', {
      action: 'create_trivia',
      username,
      trivia_question: question,
      trivia_answer: answer,
      difficulty: parseInt(difficulty, 10)
    })
    .then(response => {
      console.log('Trivia Created:', response.data);
      navigation.goBack();
    })
    .catch(err => {
      console.error('Create Error:', err.response?.data || err.message);
    });
  };

  return (
    <View>
      <Text>New Trivia</Text>
      <TextInput
        placeholder="Question"
        value={question}
        onChangeText={setQuestion}
      />
      <TextInput
        placeholder="Answer"
        value={answer}
        onChangeText={setAnswer}
      />
      <TextInput
        placeholder="Difficulty"
        value={difficulty}
        onChangeText={setDifficulty}
        keyboardType="numeric"
      />
      <Button title="Create" onPress={handleCreate} />
    </View>
  );
}
