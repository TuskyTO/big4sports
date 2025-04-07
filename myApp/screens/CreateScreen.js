// Import from React and React Native
import React, { useState } from 'react';
import { View, Text, TextInput, Button } from 'react-native';
// Import Axios for making HTTP requests to the backend
import axios from 'axios';

// This is the CreateScreen component which allows a user to create a new trivia question
export default function CreateScreen({ navigation, route }) {
  const { loggedInUser } = route.params || {};
  const [question, setQuestion] = useState('');
  const [answer, setAnswer] = useState('');
  const [difficulty, setDifficulty] = useState('1');
  const username = loggedInUser;
  const handleCreate = () => {
    // Make a POST request using Axios to our backend API endpoint
    axios.post('http://10.0.2.2/big4sports/backend/api_trivia.php', {
      action: 'create_trivia',
      username,
      trivia_question: question,
      trivia_answer: answer,
      difficulty: parseInt(difficulty, 10)
    })
    .then(response => {
      console.log('Trivia Created:', response.data);
      // Navigate back to the previous screen (e.g., HomeScreen)
      navigation.goBack();
    })
    .catch(err => {
      console.error('Create Error:', err.response?.data || err.message);
    });
  };

  return (
    <View>
      <Text>New Trivia</Text>
      
      {/* TextInput for the trivia question */}
      <TextInput
        placeholder="Question"
        value={question}
        onChangeText={setQuestion} 
      />
      
      {/* TextInput for the trivia answer */}
      <TextInput
        placeholder="Answer"
        value={answer}
        onChangeText={setAnswer} 
      />
      
      {/* TextInput for the difficulty level, which is numeric */}
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
